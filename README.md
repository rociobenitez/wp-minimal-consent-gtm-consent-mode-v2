# WP Minimal Consent (GTM + Consent Mode v2)

Plugin mínimo de WordPress CMP que integra **Google Consent Mode v2** con un **banner de cookies**, **compatibilidad con GTM** y **configuración por código** (sin interfaz de administrador).

## Características

- Consent Mode v2 (`ad_storage`, `analytics_storage`, `ad_user_data`, `ad_personalization`)
- Valores predeterminados en Modo Avanzado antes de GTM
- Valores predeterminados regionales (EEE/UK/CH)
- Banner + panel granular (Analytics/Marketing)
- Preferencias persistentes + botón flotante "Preferencias"
- Logs de depuración opcionales (`WPMC_DEBUG`)

## Requisitos

- WordPress 5.9+ / PHP 7.4+
- Contenedor de Google Tag Manager (Web)
- (Opcional) Propiedad de prueba GA4

## Instalación

1. Copiar la carpeta `wp-minimal-consent` en `wp-content/plugins/`.
2. Activar el plugin en **WP → Plugins**.
3. Editar las constantes en la parte superior de `wp-minimal-consent.php`:

- `WPMC_GTM_ID`: tu ID de contenedor GTM
- textos, posición, `WPMC_DEBUG`, etc.

## Funcionamiento

- Establece `consent default` (denegado + región + wait_for_update) **antes** de GTM.
- Restaura la elección del usuario desde `localStorage` y envía `consent update`.
- Banner: atajos Aceptar / Rechazar.
- Panel de preferencias: los toggles se mapean a:
  - Analytics → `analytics_storage`
  - Marketing → `ad_storage`, `ad_user_data`, `ad_personalization`

## Desarrollo / Depuración

- Habilitar `WPMC_DEBUG` para imprimir cada `consent default/update` en consola.
- Probar con Local (WP Engine) **Live Links** + Vista previa de GTM.

## Instrucciones para Google Tag Manager (GTM)

Consultar [gtm_instructions.md](gtm_instructions.md).
