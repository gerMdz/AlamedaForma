# Generador de formularios FORMA

El Generador de formularios FORMA es un proyecto codificado en PHP 8.1 que permite generar formularios para el concepto de FORMA utilizado en algunas iglesias cristianas.

## Requisitos

- Node.js 18 LTS o superior (se recomienda 18.x o 20.x). Este proyecto usa Vite 5, que requiere Node >= 18.
- npm 9 o superior (recomendado) o el que viene con tu Node 18 LTS.

Cómo verificar:
- `node -v` → debe mostrar v18.x.x (o mayor)
- `npm -v`
- Opcional: `npx vite --version` para confirmar que Vite está disponible

Notas de compatibilidad:
- Con Node 18 no deberías tener problemas con Vite 5 ni con `@vitejs/plugin-vue` actuales.
- En devDependencies existen paquetes heredados (por ejemplo `node-sass` y `fibers`) que ya no son necesarios para Vite y pueden fallar su instalación en algunos entornos. Si no los usas, se pueden eliminar; si solo vas a ejecutar en servidor (no compilar assets), puedes instalar sin dependencias de desarrollo: `npm ci --omit=dev`.

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