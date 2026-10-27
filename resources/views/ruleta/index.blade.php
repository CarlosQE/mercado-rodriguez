@extends('Layout.layout')

@section('title', 'Ruleta de preguntas')
@section('header', 'Ruleta de preguntas')
@section('subtitle', 'Gira, responde y descubre cuánto sabes sobre la web y el mercado.')

@push('styles')
    <style>
        .ruleta-layout { display: grid; grid-template-columns: minmax(0, 350px) minmax(0, 1fr); gap: clamp(28px, 5vw, 60px); align-items: start; }
        .ruleta-juego { display: flex; flex-direction: column; align-items: center; min-width: 0; }
        .ruleta-marcador { display: flex; flex-wrap: wrap; justify-content: center; gap: 8px; margin-bottom: 20px; }
        .ruleta-marcador span { display: inline-flex; align-items: baseline; gap: 6px; padding: 8px 16px; border-radius: 9999px; background: var(--mist); color: var(--slate); font-size: 14px; }
        .ruleta-marcador strong { color: var(--ink); font-size: 17px; font-variant-numeric: tabular-nums; }
        .ruleta-escenario { position: relative; width: min(100%, 340px); margin: 4px auto 24px; filter: drop-shadow(0 22px 30px rgba(4,23,43,.14)); }
        .ruleta { display: block; width: 100%; height: auto; transform: rotate(0deg); transform-origin: 50% 50%; }
        .ruleta-flecha { position: absolute; top: -2px; left: 50%; z-index: 2; width: 0; height: 0; border-right: 12px solid transparent; border-left: 12px solid transparent; border-top: 24px solid var(--sienna); transform: translateX(-50%); filter: drop-shadow(0 3px 3px rgba(4,23,43,.2)); }
        .ruleta-etiqueta { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; font-size: 11px; font-weight: 500; letter-spacing: -0.01em; }
        .ruleta-girar { min-width: 190px; justify-content: center; gap: 8px; padding: 13px 28px; font-size: 17px; }
        .ruleta-girar:disabled { cursor: wait; opacity: .64; transform: none; }
        .ruleta-girar svg { width: 17px; height: 17px; flex-shrink: 0; }
        .ruleta-ayuda { max-width: 290px; margin-top: 12px; color: var(--slate); font-size: 14px; text-align: center; }
        .ruleta-panel { min-width: 0; padding: 32px; border-radius: 24px; background: var(--mist); }
        .ruleta-espera { padding: 28px 12px; color: var(--slate); text-align: center; }
        .ruleta-categoria { display: inline-flex; width: fit-content; padding: 4px 12px; border-radius: 9999px; background: var(--peach); color: var(--sienna); font-size: 13px; }
        .ruleta-pregunta h2 { margin: 14px 0 22px; font-size: 29px; line-height: 1.22; letter-spacing: -0.02em; }
        .ruleta-pregunta h2:focus-visible { outline: 2px solid var(--sienna); outline-offset: 4px; border-radius: 4px; }
        .opciones-ruleta { display: flex; flex-direction: column; gap: 10px; }
        .opcion-ruleta { width: 100%; padding: 14px 18px; border: 1px solid var(--line); border-radius: 16px; background: var(--paper); color: var(--ink); font: inherit; text-align: left; cursor: pointer; transition: background-color 180ms ease-out, border-color 180ms ease-out, color 180ms ease-out, transform 180ms ease-out; }
        .opcion-ruleta:hover:not(:disabled) { background: var(--fog); transform: translateY(-1px); }
        .opcion-ruleta:disabled { cursor: default; }
        .opcion-ruleta.correcta { border-color: #a9cdb3; background: #e8f3ea; color: #24502f; }
        .opcion-ruleta.incorrecta { border-color: #dfb0b0; background: #f9e4e4; color: #6d2727; }
        .ruleta-feedback { margin-top: 20px; padding-top: 18px; border-top: 1px solid #dedee1; color: #33363b; font-size: 15px; }
        .ruleta-feedback strong { display: block; margin-bottom: 4px; color: var(--ink); }
        .ruleta-otra { margin-top: 20px; }
        .ruleta-layout [hidden] { display: none !important; }

        @media (max-width: 900px) {
            .ruleta-layout { grid-template-columns: minmax(0, 1fr); gap: 30px; }
            .ruleta-panel { width: 100%; max-width: 640px; margin: 0 auto; }
        }

        @media (max-width: 640px) {
            .ruleta-escenario { width: min(100%, 320px); }
            .ruleta-panel { padding: 24px; }
            .ruleta-pregunta h2 { font-size: 23px; }
            .ruleta-otra { width: 100%; justify-content: center; }
        }
    </style>
@endpush

@section('content')
    @php
        $paleta = [
            ['fondo' => '#fbe1d1', 'texto' => '#5d2a1a'],
            ['fondo' => '#f2f2f3', 'texto' => '#17191c'],
            ['fondo' => '#17191c', 'texto' => '#ffffff'],
            ['fondo' => '#fafafb', 'texto' => '#17191c'],
            ['fondo' => '#fbe1d1', 'texto' => '#5d2a1a'],
            ['fondo' => '#f2f2f3', 'texto' => '#17191c'],
            ['fondo' => '#17191c', 'texto' => '#ffffff'],
            ['fondo' => '#e6e6e8', 'texto' => '#17191c'],
        ];
        $paso = 360 / count($categorias);
    @endphp

    <div class="ruleta-layout">
        <section class="ruleta-juego" aria-label="Ruleta de preguntas">
            <div class="ruleta-marcador" aria-label="Marcador">
                <span><strong id="ruleta-aciertos">0</strong> aciertos</span>
                <span><strong id="ruleta-respondidas">0</strong> respuestas</span>
            </div>

            <div class="ruleta-escenario">
                <div class="ruleta-flecha" aria-hidden="true"></div>
                <svg class="ruleta" id="ruleta-rueda" viewBox="0 0 320 320" role="img" aria-labelledby="ruleta-titulo-rueda">
                    <title id="ruleta-titulo-rueda">Ruleta con ocho categorías de preguntas sobre desarrollo web y el mercado</title>

                    @foreach ($categorias as $indice => $categoria)
                        @php
                            $color = $paleta[$indice % count($paleta)];
                            $inicio = $indice * $paso;
                            $fin = $inicio + $paso;
                            $x1 = 160 + 140 * sin(deg2rad($inicio));
                            $y1 = 160 - 140 * cos(deg2rad($inicio));
                            $x2 = 160 + 140 * sin(deg2rad($fin));
                            $y2 = 160 - 140 * cos(deg2rad($fin));
                            $centro = deg2rad($inicio + ($paso / 2));
                            $textoX = 160 + 95 * sin($centro);
                            $textoY = 160 - 95 * cos($centro) + 4;
                        @endphp
                        <path
                            d="M160 160 L{{ number_format($x1, 2, '.', '') }} {{ number_format($y1, 2, '.', '') }} A140 140 0 0 1 {{ number_format($x2, 2, '.', '') }} {{ number_format($y2, 2, '.', '') }} Z"
                            fill="{{ $color['fondo'] }}"
                            stroke="#ffffff"
                            stroke-width="2"
                        />
                        <text class="ruleta-etiqueta" x="{{ number_format($textoX, 2, '.', '') }}" y="{{ number_format($textoY, 2, '.', '') }}" text-anchor="middle" fill="{{ $color['texto'] }}">{{ $categoria['corta'] }}</text>
                    @endforeach

                    <circle cx="160" cy="160" r="143" fill="none" stroke="#17191c" stroke-width="2" />
                    <circle cx="160" cy="160" r="46" fill="#ffffff" stroke="#ececec" />
                    <text x="160" y="167" text-anchor="middle" fill="#17191c" style="font-family: 'Source Serif 4', Georgia, serif; font-size: 22px">?</text>
                </svg>
            </div>

            <button type="button" class="btn ruleta-girar" id="ruleta-girar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 11a8 8 0 1 0-2.3 5.7"></path>
                    <path d="M20 4v7h-7"></path>
                </svg>
                <span>Girar</span>
            </button>
            <p class="ruleta-ayuda">Cada giro te da una pregunta nueva. Acierta y sigue jugando.</p>
        </section>

        <section class="ruleta-panel" id="ruleta-resultado" aria-live="polite" aria-busy="false">
            <p class="ruleta-espera">Gira la ruleta para recibir tu primera pregunta.</p>
        </section>
    </div>
@endsection

@section('script')
    <script>
        (() => {
            const categorias = @json($categorias);
            const preguntas = @json($preguntas);
            const categoriasPorClave = Object.fromEntries(categorias.map((categoria) => [categoria.clave, categoria]));
            const rueda = document.getElementById('ruleta-rueda');
            const botonGirar = document.getElementById('ruleta-girar');
            const resultado = document.getElementById('ruleta-resultado');
            const marcadorAciertos = document.getElementById('ruleta-aciertos');
            const marcadorRespondidas = document.getElementById('ruleta-respondidas');
            const movimientoReducido = window.matchMedia('(prefers-reduced-motion: reduce)');
            const preguntasUsadas = {};
            let rotacion = 0;
            let girando = false;
            let aciertos = 0;
            let respondidas = 0;
            let temporizador = null;

            const mezclar = (lista) => {
                const copia = [...lista];

                for (let i = copia.length - 1; i > 0; i -= 1) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [copia[i], copia[j]] = [copia[j], copia[i]];
                }

                return copia;
            };

            const elegirPregunta = (clave) => {
                const banco = preguntas[clave] || [];

                if (!banco.length) return null;

                if (!preguntasUsadas[clave]) preguntasUsadas[clave] = [];
                if (preguntasUsadas[clave].length >= banco.length) preguntasUsadas[clave] = [];

                const disponibles = banco
                    .map((_, indice) => indice)
                    .filter((indice) => !preguntasUsadas[clave].includes(indice));
                const elegido = disponibles[Math.floor(Math.random() * disponibles.length)];

                preguntasUsadas[clave].push(elegido);

                return banco[elegido];
            };

            const finalizarGiro = () => {
                if (!girando) return;

                window.clearTimeout(temporizador);
                temporizador = null;
                girando = false;
                botonGirar.disabled = false;
                botonGirar.querySelector('span').textContent = 'Girar';
                resultado.setAttribute('aria-busy', 'false');
                mostrarPregunta(categoriaGanadora.clave);
            };

            let categoriaGanadora = categorias[0];

            const mostrarPregunta = (clave) => {
                const categoria = categoriasPorClave[clave];
                const pregunta = elegirPregunta(clave);

                if (!categoria || !pregunta) return;

                const articulo = document.createElement('article');
                articulo.className = 'ruleta-pregunta';
                articulo.setAttribute('aria-labelledby', 'ruleta-pregunta-actual');

                const etiqueta = document.createElement('span');
                etiqueta.className = 'ruleta-categoria';
                etiqueta.textContent = categoria.nombre;

                const titulo = document.createElement('h2');
                titulo.className = 'serif';
                titulo.id = 'ruleta-pregunta-actual';
                titulo.tabIndex = -1;
                titulo.textContent = pregunta.pregunta;

                const opciones = document.createElement('div');
                opciones.className = 'opciones-ruleta';

                const feedback = document.createElement('div');
                feedback.className = 'ruleta-feedback';
                feedback.hidden = true;

                const botonOtra = document.createElement('button');
                botonOtra.type = 'button';
                botonOtra.className = 'btn ruleta-otra';
                botonOtra.textContent = 'Girar otra vez';
                botonOtra.hidden = true;
                botonOtra.addEventListener('click', iniciarGiro);

                let respondida = false;
                const opcionesMezcladas = mezclar(pregunta.opciones).map((opcion) => {
                    const boton = document.createElement('button');
                    boton.type = 'button';
                    boton.className = 'opcion-ruleta';
                    boton.textContent = opcion.texto;
                    boton.addEventListener('click', () => responder(boton, opcion, opcionesMezcladas, feedback, botonOtra));

                    return { boton, opcion };
                });

                opcionesMezcladas.forEach(({ boton }) => opciones.append(boton));

                const responder = (botonElegido, opcionElegida, todasLasOpciones, zonaFeedback, otra) => {
                    if (respondida) return;

                    respondida = true;
                    const esCorrecta = opcionElegida.correcta;

                    todasLasOpciones.forEach(({ boton, opcion }) => {
                        boton.disabled = true;
                        if (opcion.correcta) boton.classList.add('correcta');
                        if (boton === botonElegido && !opcion.correcta) boton.classList.add('incorrecta');
                    });

                    if (esCorrecta) aciertos += 1;
                    respondidas += 1;
                    marcadorAciertos.textContent = aciertos;
                    marcadorRespondidas.textContent = respondidas;

                    const veredicto = document.createElement('strong');
                    veredicto.textContent = esCorrecta ? '¡Correcto!' : 'Casi. La respuesta correcta está marcada en verde.';

                    const explicacion = document.createElement('span');
                    explicacion.textContent = pregunta.explicacion;

                    zonaFeedback.replaceChildren(veredicto, explicacion);
                    zonaFeedback.hidden = false;
                    otra.hidden = false;
                    otra.focus();
                };

                articulo.append(etiqueta, titulo, opciones, feedback, botonOtra);
                resultado.replaceChildren(articulo);
                titulo.focus();
            };

            const iniciarGiro = () => {
                if (girando) return;

                girando = true;
                window.clearTimeout(temporizador);
                temporizador = null;
                botonGirar.disabled = true;
                botonGirar.querySelector('span').textContent = 'Girando…';
                resultado.setAttribute('aria-busy', 'true');

                const estado = document.createElement('p');
                estado.className = 'ruleta-espera';
                estado.textContent = 'Girando la ruleta…';
                resultado.replaceChildren(estado);

                const indiceGanador = Math.floor(Math.random() * categorias.length);
                categoriaGanadora = categorias[indiceGanador];
                const paso = 360 / categorias.length;
                const centro = indiceGanador * paso + (paso / 2);
                const objetivo = (360 - (centro % 360)) % 360;
                const actual = ((rotacion % 360) + 360) % 360;
                const ajuste = (objetivo - actual + 360) % 360;

                rotacion += (360 * 5) + ajuste;

                if (movimientoReducido.matches) {
                    rueda.style.transition = 'none';
                    rueda.style.transform = `rotate(${rotacion}deg)`;
                    finalizarGiro();
                    return;
                }

                rueda.style.transition = 'transform 4s cubic-bezier(.12,.78,.16,1)';
                rueda.style.transform = `rotate(${rotacion}deg)`;
                temporizador = window.setTimeout(finalizarGiro, 4300);
            };

            rueda.addEventListener('transitionend', (evento) => {
                if (evento.propertyName === 'transform') finalizarGiro();
            });

            botonGirar.addEventListener('click', iniciarGiro);
        })();
    </script>
@endsection
