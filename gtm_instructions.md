# Instrucciones para Google Tag Manager (GTM)

### Google tags (GA4, Ads, Floodlight, etc.)

- No bloquear el Google tag.
- No añadir etiquetas/plantillas de "Consent default/update".
- Revisar **Consent settings** de cada tag (por defecto respetan Consent Mode).
- Habilita **Consent Overview** (Admin → Container Settings → Enable consent overview) para auditar qué consents requiere cada tag.

### No-Google tags

- Crear **variables** de "Consent State":

  ```
  {{CONSENT – analytics_storage}}
  {{CONSENT – ad_storage}}
  {{CONSENT – ad_user_data}}
  {{CONSENT – ad_personalization}}
  ```

- Define dos **variables derivadas** (evita repetir condiciones en cada tag):

  ```
  {{WPMC – AnalyticsGranted}} → {{CONSENT – analytics_storage}} = granted
  ```

  ```
  {{WPMC – MarketingGranted}} → {{CONSENT – ad_storage}} = granted
  AND {{CONSENT – ad_user_data}} = granted
  AND {{CONSENT – ad_personalization}} = granted
  ```

- En cada etiqueta, añadir **condición**:

  - **Analytics (Clarity/Hotjar)**: `{{WPMC – AnalyticsGranted}} = true`
    - Crear nuevo **trigger** de la etiqueta:
      - Tipo: **Page View**
      - Este trigger se activa en: **Some Page Views**
      - Condición: `{{WPMC – AnalyticsGranted}} = true`
      - Nombrar el trigger como **"Page View – Analytics Granted"**
    - Asignar este trigger a las etiquetas de Analytics _(Clarity, Hotjar...)_.
  - **Marketing (Meta/TikTok/Linkedin)**: `{{WPMC – MarketingGranted}} = true`
    - Crear nuevo **trigger** de la etiqueta:
      - Tipo: **Page View**
      - Este trigger se activa en: **Some Page Views**
      - Condición: `{{WPMC – MarketingGranted}} = true`
      - Nombrar el trigger como **"Page View – Marketing Granted"**
    - Asignar este trigger a las etiquetas de Marketing _(Meta, TikTok, Linkedin...)_.

- Crear el trigger **"WPMC – Consent update"**:
  - Tipo: Evento personalizado _("Custom Event")_
  - Nombre del evento: `wpmc_consent_update`
  - Esto permite disparar etiquetas cuando el usuario actualiza su consentimiento (opcional).
