<?php
/**
 * Plugin Name: WP Minimal Consent (GTM + Consent Mode v2)
 * Description: Banner minimalista de cookies con Google Consent Mode v2, gestionado por código y compatible con GTM (Advanced Mode).
 * Version: 0.1.1
 * Author: Rocío Benítez García
 * Require at least: 5.9
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-minimal-consent
 */

if (!defined('ABSPATH')) exit;

/** =========================
 *  CONFIGURACIÓN
 *  Edita únicamente esta sección
 *  ========================= */
define('WPMC_GTM_ID', 'GTM-K6BJZF9R');              // ID de contenedor de Google Tag Manager
define('WPMC_STORAGE_KEY', 'wpmc_consent');         // Clave en localStorage para guardar el estado
define('WPMC_EVENT_UPDATE', 'wpmc_consent_update'); // Nombre del evento en dataLayer al actualizar consentimiento
define('WPMC_WAIT_FOR_UPDATE', 500);                // ms para wait_for_update (Consent Mode)
define('WPMC_BANNER_POSITION', 'bottom');           // 'bottom' | 'top'
define('WPMC_BANNER_SHOW_MANAGE', true);            // Mostrar botón "Gestionar"
define('WPMC_FLOATING_ENABLED', true);              // Mostrar botón "flotante"
define('WPMC_FLOATING_CORNER', 'bottom-left');      // 'bottom-right' | 'bottom-left'
define('WPMC_DEBUG', true);                         // Activar logs en consola para depuración

// Textos del banner
define('WPMC_TXT_TITLE', 'Valoramos tu privacidad');
define('WPMC_TXT_MSG', 'Usamos cookies para mejorar su experiencia de navegación,  mostrarle anuncios o contenidos personalizados y analizar nuestro tráfico. Al hacer clic en “Aceptar todo” usted da su consentimiento a nuestro uso de las cookies.');
define('WPMC_TXT_ACCEPT', 'Aceptar todo');
define('WPMC_TXT_REJECT', 'Rechazar');
define('WPMC_TXT_MANAGE', 'Gestionar');
define('WPMC_TXT_PREFS', 'Preferencias de cookies');

// Textos del panel de preferencias
define('WPMC_TXT_PANEL_TITLE', 'Preferencias de cookies');
define('WPMC_TXT_PANEL_DESC', 'Activa o desactiva categorías. Siempre usamos cookies necesarias.');
define('WPMC_TXT_CAT_ANALYTICS', 'Analítica');
define('WPMC_TXT_CAT_ANALYTICS_DESC', 'Las cookies analíticas se utilizan para comprender cómo interactúan los visitantes con el sitio web. Estas cookies ayudan a proporcionar información sobre métricas el número de visitantes, el porcentaje de rebote, la fuente de tráfico, etc.');
define('WPMC_TXT_CAT_MARKETING', 'Publicidad');
define('WPMC_TXT_CAT_MARKETING_DESC', 'Las cookies de publicidad se utilizan para mostrar anuncios personalizados y compartir datos con terceros (Google Ads, Facebook Ads...)');
define('WPMC_TXT_SAVE', 'Guardar selección');
define('WPMC_TXT_CLOSE', 'Cerrar');

/** =========================
 *  AVISOS EN ADMIN
 *  ========================= */
add_action('admin_notices', function () {
  if (!current_user_can('manage_options')) return;
  if (!WPMC_GTM_ID || WPMC_GTM_ID === 'GTM-XXXXXXX') {
    echo '<div class="notice notice-error"><p><strong>WP Minimal Consent:</strong> configura tu <code>WPMC_GTM_ID</code> en el archivo del plugin.</p></div>';
  }
});

/** =========================
 *  CSS del banner
 *  ========================= */
add_action('wp_enqueue_scripts', function () {
  wp_enqueue_style(
    'wpmc-banner-styles',
    plugins_url('assets/banner.css', __FILE__),
    [],
    '0.1.0'
  );
});

/** =========================
 *  HEAD: dataLayer + Consent defaults + carga GTM
 *  ========================= */
add_action('wp_head', function () {
  $gtm = esc_js(WPMC_GTM_ID);
  $waitMs = (int) WPMC_WAIT_FOR_UPDATE;
  $storageKey = esc_js(WPMC_STORAGE_KEY);
  ?>
  <script>
    // Debug global
    window.WPMC_DEBUG = <?php echo WPMC_DEBUG ? 'true' : 'false'; ?>;

    // dataLayer + gtag()
    window.dataLayer = window.dataLayer || [];
    function gtag(){ 
      dataLayer.push(arguments);
      if (window.WPMC_DEBUG && arguments && arguments[0] === 'consent') {
        try {
          const type = arguments[1]
          const payload = arguments[2] || {};
          console.groupCollapsed('[WPMC] consent ' + type);
          console.table(payload);
          console.groupEnd();
        } catch(e){}
      }
    }

    // Señales recomendadas
    gtag('set', 'ads_data_redaction', true);
    gtag('set', 'url_passthrough', true);

    // Defaults (Advanced Consent Mode) ANTES de cargar GTM
    gtag('consent', 'default', {
      ad_storage: 'denied',
      analytics_storage: 'denied',
      ad_user_data: 'denied',
      ad_personalization: 'denied',
      wait_for_update: <?php echo $waitMs; ?>,
      // region: ["AT","BE","BG","HR","CY","CZ","DK","EE","FI","FR","DE","GR","HU","IE","IT","LV","LT","LU","MT","NL","PL","PT","RO","SK","SI","ES","SE","IS","LI","NO","GB","CH"]
    });

    // Restaurar la elección previa del usuario si existe
    ((KEY) => {
      try {
        const raw = localStorage.getItem(KEY);
        if (!raw) return;
        const c = JSON.parse(raw) || {};
        const payload = {};
        ['ad_storage','analytics_storage','ad_user_data','ad_personalization']
          .forEach(k => { if (c[k]) payload[k] = c[k]; });
        if (Object.keys(payload).length) gtag('consent','update', payload);
      } catch(e){}
    })('<?php echo $storageKey; ?>');

    // Carga de GTM tras fijar los defaults de consentimiento
    <?php if (!empty(WPMC_GTM_ID) && WPMC_GTM_ID !== 'GTM-XXXXXXX') : ?>
    (function(w,d,s,l,i){
      w[l]=w[l]||[]; w[l].push({'gtm.start': new Date().getTime(), event:'gtm.js'});
      var f=d.getElementsByTagName(s)[0], j=d.createElement(s), dl=l!='dataLayer'?'&l='+l:'';
      j.async=true; j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;
      f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','<?php echo $gtm; ?>');
    <?php endif; ?>
  </script>
  <?php
}, 0);

/** =========================
 *  BODY OPEN: <noscript> de GTM
 *  ========================= */
add_action('wp_body_open', function () {
  if (empty(WPMC_GTM_ID) || WPMC_GTM_ID === 'GTM-XXXXXXX') return;
  $gtm = esc_attr(WPMC_GTM_ID);
  echo '<noscript><iframe src="https://www.googletagmanager.com/ns.html?id='. $gtm .'" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>';
}, 0);

/** =========================
 *  FOOTER: Banner + manejadores de consentimiento
 *  ========================= */
add_action('wp_footer', function () {
  // Posición del banner
  $inset = (WPMC_BANNER_POSITION === 'top') ? '0 0 auto 0' : 'auto 0 0 0';
  // Posición del botón flotante
  $floatX = (WPMC_FLOATING_CORNER === 'bottom-left') ? 'left:16px;right:auto;' : 'right:16px;left:auto;';
  ?>
  <style>
    #wpmc-banner{position:fixed;inset:<?php echo esc_attr($inset); ?>;display:flex;gap:.75rem;align-items:center;justify-content:space-between;
      background:#111;color:#fff;padding:12px 16px;z-index:2147483646;font:14px/1.35 system-ui,-apple-system,Segoe UI,Roboto}
    /* Botón flotante de preferencias */
    #wpmc-preferences-btn{
      position:fixed; bottom:16px; <?php echo $floatX; ?>
    }
  </style>

  <!-- Consent Banner -->
  <div id="wpmc-banner" class="wpmc-banner" role="dialog" aria-live="polite" aria-label="Preferencias de cookies">
    <div>
      <?php if (WPMC_TXT_TITLE): ?>
        <h2 class="wpmc-title"><?php echo esc_html(WPMC_TXT_TITLE); ?></h2>
      <?php endif; ?>
      <div class="wpmc-desc"><?php echo esc_html(WPMC_TXT_MSG); ?></div>
    </div>
    <div class="wpmc-actions">
      <button id="wpmc-accept"><?php echo esc_html(WPMC_TXT_ACCEPT); ?></button>
      <button id="wpmc-reject"><?php echo esc_html(WPMC_TXT_REJECT); ?></button>
      <?php if (WPMC_BANNER_SHOW_MANAGE): ?>
        <button id="wpmc-manage" type="button"><?php echo esc_html(WPMC_TXT_MANAGE); ?></button>
      <?php endif; ?>
    </div>
  </div>
  <?php if (WPMC_FLOATING_ENABLED): ?>
  <button id="wpmc-preferences-btn" type="button" aria-label="<?php echo esc_attr(WPMC_TXT_PREFS); ?>" title="<?php echo esc_attr(WPMC_TXT_PREFS); ?>" aria-controls="wpmc-banner">
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path d="M12 2a10 10 0 1 0 9.54 12.9A4 4 0 0 1 17 11a4 4 0 0 1-4-4 4 4 0 0 1-1-.13A4 4 0 0 1 12 2zm-3 9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm7 4a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zM9 18a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
    </svg>
  </button>
  <?php endif; ?>

  <!-- Modal de preferencias -->
  <div id="wpmc-modal" class="wpmc-modal" role="dialog" aria-modal="true" aria-labelledby="wpmc-modal-title" hidden>
    <div class="wpmc-box">
      <?php if (WPMC_TXT_PANEL_TITLE): ?>
        <h2 id="wpmc-modal-title" class="wpmc-title" tabindex="-1"><?php echo esc_html(WPMC_TXT_PANEL_TITLE); ?></h2>
      <?php endif; ?>
      <p class="wpmc-desc"><?php echo esc_html(WPMC_TXT_PANEL_DESC); ?></p>

      <div class="wpmc-row">
        <div>
          <div class="wpmc-label"><?php echo esc_html(WPMC_TXT_CAT_ANALYTICS); ?></div>
          <div class="wpmc-desc"><?php echo esc_html(WPMC_TXT_CAT_ANALYTICS_DESC); ?></div>
        </div>
        <label>
          <input id="wpmc-opt-analytics" type="checkbox" aria-label="<?php echo esc_attr(WPMC_TXT_CAT_ANALYTICS); ?>">
          <span class="wpmc-switch"></span>
        </label>
      </div>

      <div class="wpmc-row">
        <div>
          <div class="wpmc-label"><?php echo esc_html(WPMC_TXT_CAT_MARKETING); ?></div>
          <div class="wpmc-desc"><?php echo esc_html(WPMC_TXT_CAT_MARKETING_DESC); ?></div>
        </div>
        <label>
          <input id="wpmc-opt-marketing" type="checkbox" aria-label="<?php echo esc_attr(WPMC_TXT_CAT_MARKETING); ?>">
          <span class="wpmc-switch"></span>
        </label>
      </div>

      <div class="wpmc-actions">
        <button id="wpmc-save"  class="wpmc-btn wpmc-btn--primary"><?php echo esc_html(WPMC_TXT_SAVE); ?></button>
        <button id="wpmc-close" class="wpmc-btn wpmc-btn--ghost" type="button"><?php echo esc_html(WPMC_TXT_CLOSE); ?></button>
      </div>
    </div>
  </div>

  <script>
    (() => {
      const WPMC_STORAGE_KEY  = '<?php echo json_encode(WPMC_STORAGE_KEY); ?>';
      const WPMC_EVENT_UPDATE = '<?php echo json_encode(WPMC_EVENT_UPDATE); ?>';

      const banner   = document.getElementById('wpmc-banner');
      const modal    = document.getElementById('wpmc-modal');
      const prefBtn  = document.getElementById('wpmc-preferences-btn');

      const hideIfDecided = true;
      let lastFocus = null;

      const el = {
        accept: document.getElementById('wpmc-accept'),
        reject: document.getElementById('wpmc-reject'),
        manage: document.getElementById('wpmc-manage'),
        save:   document.getElementById('wpmc-save'),
        close:  document.getElementById('wpmc-close'),
        optA:   document.getElementById('wpmc-opt-analytics'),
        optM:   document.getElementById('wpmc-opt-marketing')
      };

      // Estado inicial
      let saved = localStorage.getItem(WPMC_STORAGE_KEY);
      const hasDecision = !!saved;
      banner.hidden = hideIfDecided && hasDecision;
      if (prefBtn) prefBtn.hidden = !hasDecision;
      if (window.WPMC_DEBUG) console.info('[WPMC:UI] init', {hasDecision: hasDecision});

      const hydrateToggles = () => {
        try{
          const c = saved ? JSON.parse(saved) : null;
          el.optA.checked = c ? (c.analytics_storage === 'granted') : false;
          el.optM.checked = c ? (c.ad_storage === 'granted' && c.ad_user_data === 'granted' && c.ad_personalization === 'granted') : false;
          if (window.WPMC_DEBUG) console.log('[WPMC:UI] hydrate', {analytics: el.optA.checked, marketing: el.optM.checked});
        }catch(e){
          el.optA.checked = false;
          el.optM.checked = false;
        }
      }

      const openModal = () => {
        hydrateToggles();
        lastFocus = document.activeElement;
        modal.hidden = false;
        const title = document.getElementById('wpmc-modal-title');
        if (title) title.focus({preventScroll:true});
        if (prefBtn) prefBtn.hidden = true;
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] modal open');
      }
      const closeModal = () => {
        modal.hidden = true;
        if (lastFocus && lastFocus.focus) lastFocus.focus({preventScroll:true});
        const has = !!localStorage.getItem(WPMC_STORAGE_KEY);
        if (prefBtn) prefBtn.hidden = !has;
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] modal close');
      }

      const applyConsent = (state) => {
        localStorage.setItem(WPMC_STORAGE_KEY, JSON.stringify(state));
        if (window.WPMC_DEBUG) {
          console.groupCollapsed('[WPMC] applyConsent');
          console.table(state);
          console.groupEnd();
        }
        // Señal a Google
        gtag('consent','update', state);
        // Señal a GTM (Custom Event)
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: WPMC_EVENT_UPDATE, consent_state: state });

        saved = JSON.stringify(state);
        banner.hidden = true;
        if (prefBtn) prefBtn.hidden = false;
      }

      const computeState = () => {
        const A = el.optA.checked; // Analítica
        const M = el.optM.checked; // Publicidad
        const state = {
          analytics_storage: A ? 'granted' : 'denied',
          ad_storage:        M ? 'granted' : 'denied',
          ad_user_data:      M ? 'granted' : 'denied',
          ad_personalization:M ? 'granted' : 'denied'
        };
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] computeState', state);
        return state;
      }

      // Botones del banner
      el.accept?.addEventListener('click', () => {
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] click Aceptar todo');
        applyConsent({
          ad_storage:'granted', analytics_storage:'granted',
          ad_user_data:'granted', ad_personalization:'granted'
        });
        if (!modal.hidden) closeModal();
      });

      el.reject?.addEventListener('click', () => {
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] click Rechazar todo');
        applyConsent({
          ad_storage:'denied', analytics_storage:'denied',
          ad_user_data:'denied', ad_personalization:'denied'
        });
        if (!modal.hidden) closeModal();
      });

      el.manage?.addEventListener('click', () => {
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] click Ajustar preferencias');
        openModal();
      });

      // Botón flotante
      prefBtn?.addEventListener('click', () => {
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] click Botón flotante');
        openModal();
      });

      // Acciones del modal
      el.save?.addEventListener('click', () => {
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] click Guardar selección');
        applyConsent(computeState());
        closeModal();
      });
      el.close?.addEventListener('click', () => {
        if (window.WPMC_DEBUG) console.log('[WPMC:UI] click Cerrar');
        closeModal();
      });

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) {
          if (window.WPMC_DEBUG) console.log('[WPMC:UI] press Escape');
          closeModal();
        }
      });

      // API
      window.WPMC = {
        show: function(){ if (window.WPMC_DEBUG) console.log('[WPMC:API] show'); banner.hidden = false; if (prefBtn) prefBtn.hidden = true; },
        hide: function(){ if (window.WPMC_DEBUG) console.log('[WPMC:API] hide'); banner.hidden = true;  if (prefBtn) prefBtn.hidden = !!localStorage.getItem(KEY) ? false : true; },
        update: function(p){ if (window.WPMC_DEBUG) console.log('[WPMC:API] update', p); applyConsent(p); }
      };
    })();
  </script>
  <?php
}, 9999);