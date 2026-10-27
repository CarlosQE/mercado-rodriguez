"""Run against deploy/compose.smoke.yaml only, using Python's standard library."""
import html
import http.cookiejar
import re
import subprocess
import urllib.parse
import urllib.request

BASE = 'http://127.0.0.1:18087'
COMPOSE = ['docker', 'compose', '-p', 'mercado-hosting-check', '-f', 'deploy/compose.smoke.yaml']

def counts():
    query = ('select (select count(*) from comerciantes), '
             '(select count(*) from puestos), (select count(*) from users), '
             '(select count(*) from deployment_markers)')
    return subprocess.check_output(COMPOSE + [
        'exec', '-T', 'db', 'psql', '-U', 'smoke', '-d', 'mercado_deploy_test',
        '-At', '-c', query], text=True).strip()

assert counts() == '280|320|2|1', counts()
subprocess.run(COMPOSE + ['exec', '-T', 'web', 'php', 'artisan', 'deploy:demo'], check=True)
assert counts() == '280|320|2|1', 'The second initialization changed counts'

for email, password, destination in [
    ('admin@mercado.test', 'admin-local-test-only', '/dashboard'),
    ('cobrador@mercado.test', 'collector-local-test-only', '/mapa'),
]:
    opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
    page = opener.open(BASE + '/login', timeout=20).read().decode()
    token = html.unescape(re.search(r'name="_token"[^>]*value="([^"]+)"', page)[1])
    body = urllib.parse.urlencode({'_token': token, 'email': email, 'password': password}).encode()
    response = opener.open(BASE + '/login', data=body, timeout=20)
    assert response.status == 200
    assert urllib.parse.urlparse(response.url).path == destination, response.url
    print('PASS login:', email, destination)

for path in ['/', '/up', '/img/mercado/foto-1.svg']:
    assert urllib.request.urlopen(BASE + path, timeout=20).status == 200
print('PASS initial seed, repeated initialization, logins, public pages and sample image')
