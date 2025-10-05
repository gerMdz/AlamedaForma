# Generador de formularios FORMA

El Generador de formularios FORMA es un proyecto codificado en PHP 8.1 que permite generar formularios para el concepto de FORMA utilizado en algunas iglesias cristianas.

## Requisitos

- Node.js 22 LTS recomendado (compatible con >=20 <23). Este proyecto usa Vite 5, que requiere Node >= 18; hemos probado y recomendado Node 22.
- npm 10 o superior (o el que viene con tu Node 22 LTS).

Cómo verificar y usar la versión fijada:
- `node -v` → debería mostrar v22.x.x (usa `.nvmrc` para fijar la versión: `nvm use` o `nvm install` la primera vez).
- `npm -v`
- Opcional: `npx vite --version` para confirmar que Vite está disponible

Notas de compatibilidad:
- Con Node 22 no deberías tener problemas con Vite 5 ni con `@vitejs/plugin-vue` actuales.
- Se eliminaron dependencias nativas obsoletas (`node-sass`, `fibers`) que fallaban en Node >=18. Si ejecutas en servidores sin compilar assets, instala sin dependencias de desarrollo: `npm ci --omit=dev`.

## Instalación

1. Clona el repositorio en tu entorno local.
2. Ejecuta `composer install` para instalar las dependencias del proyecto.

## Uso

1. Ejecuta el comando (proporcionar aquí el comando relevante) para iniciar la aplicación.
2. Navega a la interfaz de usuario y selecciona la opción para generar un formulario FORMA.
3. Rellena los parámetros requeridos y envía el formulario.
4. La aplicación generará un formulario FORMA basado en los datos proporcionados.

## Pruebas

Este proyecto utiliza PHPUnit como framework de pruebas. Puedes ejecutar las pruebas usando el comando `phpunit`.

## Contribución

Si estás interesado en contribuir a este proyecto, por favor, envía un pull request o abre un issue en Github.

## Agradecimientos

Un agradecimiento especial a las siguientes herramientas y sus equipos de desarrollo, sin las cuales este proyecto no sería posible:

- [Symfony](https://symfony.com): Usamos Symfony como el marco de aplicación en el que se basa este proyecto. Symfony proporciona una serie de herramientas y bibliotecas que han hecho de la construcción de este proyecto un proceso más manejable y eficiente.

- [PhpStorm](https://www.jetbrains.com/phpstorm): PhpStorm es nuestro entorno de desarrollo integrado (IDE) de elección para este proyecto. Sus características, que incluyen la autocompleción de código y la detección de errores en tiempo real, han sido fundamentales para mantener la alta calidad del código en este proyecto.

## Licencia

Este proyecto está licenciado bajo la licencia (proporcionar detalles de la licencia).

## Resolver error de git pull con package-lock.json

Si en el servidor ves este error al hacer `git pull`:

```
error: No es posible hacer pull porque tienes archivos sin fusionar.
ayuda: Corrígelos en el árbol de trabajo y entonces usa 'git add/rm <archivo>',
ayuda: como sea apropiado, para marcar la resolución y realizar un commit.
fatal: Saliendo porque existe un conflicto sin resolver.
```

Normalmente el conflicto está en `package-lock.json`. Aquí tienes varias soluciones, de la más simple a la más cuidadosa.

1) Si NO tienes cambios locales que necesites conservar (servidor de solo despliegue)
- Este método descarta todo lo local y deja la copia idéntica a `origin/main`.
- Comandos:
  - `git merge --abort` (si el merge está en curso)
  - `git fetch origin`
  - `git reset --hard origin/main`
  - Opcional para borrar archivos no versionados: `git clean -fd`
  - Instalar dependencias tal como están fijadas en el lockfile: `npm ci --omit=dev`

2) Resolver el conflicto manteniendo la versión remota del lockfile
- Útil si quieres terminar el merge sin descartar otros cambios locales, pero aceptando el `package-lock.json` del repositorio remoto.
- Comandos (desde el estado de conflicto):
  - `git checkout --theirs package-lock.json`
  - `git add package-lock.json`
  - `git commit -m "Resolve merge: keep remote package-lock.json"`
  - `npm ci --omit=dev` (en servidores) o `npm ci` (en dev)

3) Regenerar el lockfile desde package.json
- Útil si quieres que el lockfile se reconstruya con tu versión de Node/npm.
- Comandos:
  - `git merge --abort` (si el merge está en curso)
  - `rm -rf node_modules package-lock.json`
  - `npm install`
  - `git add package-lock.json`
  - `git commit -m "Regenerate package-lock.json from package.json"`

Buenas prácticas para evitar futuros conflictos con package-lock.json
- En servidores de producción o staging: usa `npm ci --omit=dev` y evita ejecutar `npm install` para no modificar el lockfile.
- Haz cambios de dependencias solo en tu entorno de desarrollo y sube ambos archivos: `package.json` y `package-lock.json`.
- Evita hacer commits desde el servidor. Mantén el servidor como un espejo de la rama principal.
- Si los conflictos con `package-lock.json` son frecuentes, considera instalar el merge driver de npm en tu entorno de desarrollo: `npx npm-merge-driver install --global` (requiere configuración local; no es obligatorio en el servidor).


## Notas para Docker/Kubernetes (construcción de assets)

Este repositorio incluye `.nvmrc` con Node 22 y usa Vite 5. Si necesitas construir assets en contenedores o clusters:

- Imagen base recomendada para construir assets: `node:22-bookworm` (o `node:22-alpine` si tu entorno usa musl; en ese caso asegúrate de que todas las dependencias tengan binarios para Alpine).
- Comandos de build: `npm ci && npm run build`. Esto genera `public/build` (ver `vite.config.js`).

Ejemplo de Dockerfile multi-stage simplificado:

```Dockerfile
# Etapa de build de frontend
FROM node:22-bookworm AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# Etapa de runtime PHP (ajusta la imagen a tus necesidades)
FROM php:8.3-apache
# Configura DocumentRoot si es necesario y extensiones PHP según Symfony
WORKDIR /var/www/html
COPY . .
# Copia los assets construidos por Vite
COPY --from=assets /app/public/build /var/www/html/public/build
# Asegura permisos adecuados para Symfony (var/, etc.)
```

Notas para Kubernetes:
- Lo más común es construir los assets en CI y publicar una imagen ya con `public/build` embebido (como en el Dockerfile anterior).
- Alternativamente, puedes usar un `initContainer` con `node:22` que ejecute `npm ci && npm run build` y volcar `public/build` en un `emptyDir` compartido con el contenedor principal. Sin embargo, esta opción hace el build en tiempo de despliegue y suele ser más lenta.

Buenas prácticas:
- En servidores/containers de producción: usa `npm ci --omit=dev` si por alguna razón instalas deps (no recomendado en runtime). Lo ideal es generar los assets en CI.
- Evita compilar assets en caliente en producción. Compílalos en CI y versiona el resultado dentro de la imagen.
- Mantén sincronizados `package.json`/`package-lock.json`. Con Node 22, usa npm 10.
