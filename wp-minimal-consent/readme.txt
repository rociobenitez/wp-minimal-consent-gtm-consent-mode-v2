=== WP Minimal Consent (GTM + Consent Mode v2) ===
Contributors: rociobenitezgarcia
Tags: cookies, consent, gdpr, google tag manager, gtm, consent mode, analytics, privacy
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 0.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Banner minimalista de cookies con Google Consent Mode v2, gestionado por código y compatible con GTM (Advanced Mode).

== Description ==

**WP Minimal Consent** es un plugin minimalista para WordPress que implementa correctamente **Google Consent Mode v2** junto con **Google Tag Manager**. Diseñado para desarrolladores que buscan una solución ligera, eficiente y totalmente personalizable.

= Características Principales =

* **Google Consent Mode v2** implementado correctamente
* **Google Tag Manager (Advanced Mode)** totalmente compatible  
* Banner de cookies minimalista y responsive
* **Sin base de datos** - usa localStorage del navegador
* **Configuración sencilla** - solo editar constantes en PHP
* **Regiones GDPR** configuradas por defecto (EEE + UK + CH)
* API JavaScript global para integraciones personalizadas
* **Zero dependencias** externas

= ¿Por qué elegir WP Minimal Consent? =

**Para desarrolladores que quieren control total:**
- Configuración mediante constantes PHP (no UI compleja)
- Código limpio, legible y mantenible
- Filosofía MVP: funcionalidad esencial sin bloat
- Fácil de extender y personalizar

**Cumplimiento normativo:**
- GDPR compliant para regiones europeas
- Implementación correcta de Consent Mode v2
- Señales recomendadas por Google incluidas

= Configuración Rápida =

1. Instala y activa el plugin
2. Edita `wp-consent-mode.php` y configura tu GTM ID:
   `define('WPMC_GTM_ID', 'GTM-XXXXXXX');`
3. ¡Listo! El banner aparecerá automáticamente

= API JavaScript =

El plugin expone `window.WPMC` para integraciones personalizadas:

* `WPMC.show()` - Mostrar banner
* `WPMC.hide()` - Ocultar banner  
* `WPMC.update(consent)` - Actualizar consentimiento

= Personalización =

Personaliza fácilmente textos, posición y estilos editando las constantes en el archivo PHP:

* Posición del banner (top/bottom)
* Textos del mensaje y botones
* Tiempo de espera para actualizaciones
* Mostrar/ocultar botón "Gestionar"

== Installation ==

= Instalación Automática =

1. Ve a Plugins > Añadir nuevo en tu admin de WordPress
2. Busca "WP Minimal Consent"
3. Instala y activa el plugin

= Instalación Manual =

1. Descarga el plugin desde WordPress.org
2. Sube la carpeta `wp-consent-mode` a `/wp-content/plugins/`
3. Activa el plugin desde el menú Plugins

= Configuración Obligatoria =

**¡IMPORTANTE!** Debes configurar tu ID de Google Tag Manager:

1. Edita el archivo `wp-consent-mode.php`
2. Cambia esta línea:
   `define('WPMC_GTM_ID', 'GTM-XXXXXXX');`
3. Reemplaza `GTM-XXXXXXX` por tu ID real de GTM

= Configuración en Google Tag Manager =

1. Activa **Advanced Consent Mode** en tu contenedor GTM
2. Configura tus tags para que respeten las señales de consentimiento:
   - Tags de Analytics: requieren `analytics_storage` 
   - Tags de Ads: requieren `ad_storage`, `ad_user_data`, `ad_personalization`

== Frequently Asked Questions ==

= ¿Necesito configurar algo en Google Tag Manager? =

Sí, debes activar el **Advanced Consent Mode** en tu contenedor GTM y configurar tus tags para que respeten las señales de consentimiento.

= ¿El plugin cumple con GDPR? =

Sí, el plugin establece "denied" por defecto para todas las categorías de cookies en regiones GDPR (UE, UK, CH, etc.) y solo otorga permisos tras el consentimiento explícito del usuario.

= ¿Puedo personalizar el banner? =

Sí, puedes personalizar textos, posición y estilos editando las constantes en `wp-consent-mode.php`. Para estilos avanzados, puedes sobrescribir el CSS desde tu tema.

= ¿El banner es responsive? =

Sí, el banner se adapta automáticamente a dispositivos móviles cambiando de disposición horizontal a vertical en pantallas menores a 640px.

= ¿Dónde se guardan las preferencias del usuario? =

Las preferencias se guardan en el localStorage del navegador del usuario. No se almacena nada en la base de datos de WordPress.

= ¿Puedo integrarlo con otros sistemas? =

Sí, el plugin expone una API JavaScript global (`window.WPMC`) que permite mostrar/ocultar el banner y actualizar el consentimiento programáticamente.

= ¿Qué pasa si el usuario no tiene JavaScript habilitado? =

El plugin incluye el noscript de GTM requerido. Sin JavaScript, simplemente no se mostrará el banner de cookies, pero GTM funcionará con los defaults "denied" establecidos.

== Screenshots ==

1. Banner de cookies en la parte inferior (desktop)
2. Banner responsive en móvil (disposición vertical)  
3. Configuración sencilla mediante constantes PHP
4. Vista del código limpio y bien documentado

== Changelog ==

= 0.1.1 =
* Versión inicial pública
* Implementación completa de Google Consent Mode v2
* Banner minimalista responsive
* API JavaScript para integraciones
* Configuración mediante constantes PHP
* Compatible con GTM Advanced Mode
* Regiones GDPR preconfiguradas

== Upgrade Notice ==

= 0.1.1 =
Versión inicial del plugin. Configura tu GTM ID tras la activación.

== Developer Notes ==

= Filosofía MVP =

Este plugin sigue la filosofía **Minimum Viable Product**:
- Funcionalidad esencial sin características innecesarias
- Configuración mediante código (más flexible que UI)
- Zero dependencias externas
- Código legible y fácil de mantener

= Extensibilidad =

El plugin está diseñado para ser extendido:
- Usa hooks estándar de WordPress
- Código bien documentado y estructurado
- API JavaScript pública para integraciones
- Constantes PHP para toda la configuración

= Hooks Disponibles =

El plugin utiliza hooks estándar de WordPress:
- `wp_head` (prioridad 0) - Consent defaults y carga GTM
- `wp_body_open` (prioridad 0) - Noscript de GTM  
- `wp_footer` (prioridad 9999) - Banner y JavaScript
- `admin_notices` - Avisos de configuración

= Soporte para Desarrolladores =

¿Necesitas funciones específicas? Usa este plugin como base sólida y extiéndelo según tus necesidades. El código está optimizado para ser modificado y ampliado fácilmente.
