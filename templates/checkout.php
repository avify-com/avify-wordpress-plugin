<?php
$options = get_option('avify-settings-options');
if (($options['avify_enable_checkout'] ?? '') !== 'on') {
    echo do_shortcode('[woocommerce_checkout]');
    return;
}

$svgCheck = '<div class="step-item-circle">
                <div class="step-item-circle-check">
                    <svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                         xmlns="http://www.w3.org/2000/svg">
                        <path d="M25 12.5C25 15.8152 23.683 18.9946 21.3388 21.3388C18.9946 23.683 15.8152 25 12.5 25C9.18479 25 6.00537 23.683 3.66117 21.3388C1.31696 18.9946 0 15.8152 0 12.5C0 9.18479 1.31696 6.00537 3.66117 3.66117C6.00537 1.31696 9.18479 0 12.5 0C15.8152 0 18.9946 1.31696 21.3388 3.66117C23.683 6.00537 25 9.18479 25 12.5V12.5ZM18.7969 7.76562C18.6853 7.6544 18.5524 7.56683 18.4061 7.50815C18.2599 7.44946 18.1033 7.42087 17.9458 7.42408C17.7883 7.42729 17.633 7.46223 17.4893 7.52683C17.3456 7.59142 17.2164 7.68433 17.1094 7.8L11.6828 14.7141L8.4125 11.4422C8.19035 11.2352 7.89653 11.1225 7.59293 11.1279C7.28934 11.1332 6.99967 11.2562 6.78497 11.4709C6.57026 11.6856 6.44727 11.9753 6.44191 12.2789C6.43656 12.5825 6.54925 12.8763 6.75625 13.0984L10.8906 17.2344C11.002 17.3455 11.1346 17.4332 11.2806 17.492C11.4266 17.5508 11.5829 17.5796 11.7402 17.5767C11.8976 17.5737 12.0527 17.5392 12.1964 17.475C12.3401 17.4108 12.4694 17.3184 12.5766 17.2031L18.8141 9.40625C19.0267 9.18515 19.1442 8.88949 19.1413 8.58275C19.1383 8.276 19.0153 7.98263 18.7984 7.76562H18.7969Z"
                              fill="limegreen"/>
                    </svg>
                </div>
            </div>';

$svgAttach = '<div class="yv-file-uploder-image">
                <svg width="48" height="42" viewBox="0 0 48 42" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M40.875 0.75H7.125C5.33539 0.751954 3.61963 1.46374 2.35419 2.72919C1.08874 3.99464 0.376954 5.71039 0.375 7.5V34.5C0.376954 36.2896 1.08874 38.0054 2.35419 39.2708C3.61963 40.5363 5.33539 41.248 7.125 41.25H40.875C42.6646 41.248 44.3804 40.5363 45.6458 39.2708C46.9113 38.0054 47.623 36.2896 47.625 34.5V7.5C47.623 5.71039 46.9113 3.99464 45.6458 2.72919C44.3804 1.46374 42.6646 0.751954 40.875 0.75ZM32.4375 7.5C33.4388 7.5 34.4175 7.79691 35.2501 8.35319C36.0826 8.90946 36.7315 9.70011 37.1146 10.6252C37.4978 11.5502 37.5981 12.5681 37.4027 13.5501C37.2074 14.5322 36.7252 15.4342 36.0172 16.1422C35.3092 16.8502 34.4072 17.3324 33.4251 17.5277C32.4431 17.7231 31.4252 17.6228 30.5002 17.2396C29.5751 16.8565 28.7845 16.2076 28.2282 15.3751C27.6719 14.5426 27.375 13.5638 27.375 12.5625C27.3764 11.2203 27.9102 9.93341 28.8593 8.98431C29.8084 8.03521 31.0953 7.5014 32.4375 7.5ZM7.125 37.875C6.22989 37.875 5.37145 37.5194 4.73851 36.8865C4.10558 36.2535 3.75 35.3951 3.75 34.5V27.3671L13.7527 18.4761C14.7177 17.6202 15.9729 17.1644 17.2623 17.2017C18.5517 17.2389 19.7784 17.7663 20.6925 18.6765L27.5427 25.512L15.1796 37.875H7.125ZM44.25 34.5C44.25 35.3951 43.8944 36.2535 43.2615 36.8865C42.6285 37.5194 41.7701 37.875 40.875 37.875H19.9532L32.7592 25.069C33.6659 24.2979 34.8164 23.8731 36.0066 23.8701C37.1968 23.867 38.3496 24.2858 39.2603 25.0521L44.25 29.2097V34.5Z" fill="#00543B"/>
                </svg>
              </div>';

$cart = WC()->cart;
?>

<div>
    <script>
        let avfIsVirtual = '<?= $cart && !$cart->needs_shipping() ?>';
        let avfAttachmentRequired = '<?= ($options['avify_attachment_required'] ?? '') === 'on' ?>';
        let avfShowElectronicInvoice = '<?= ($options['avify_show_electronic_invoice'] ?? '') === 'on' ?>';
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&family=Open+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
</div>
<section class="type-woo-checkout">
    <div class="wrapper">
        <div class="content">
            <?= do_shortcode('[woocommerce_checkout]'); ?>
        </div>
    </div>
</section>
<section class="type-avify-checkout">
    <div class="wrapper">
        <div class="content">
            <div class="step-list">
                <div class="step-item">
                    <?= $svgCheck ?>

                    <div class="step-item-text">
                        <div class="avf_txt type-2">
                            <?php _e('Datos personales',  'avify-wordpress'); ?>
                        </div>
                    </div>
                </div>

                <?php if($cart && (!$cart->get_cart() || $cart->needs_shipping())): ?>
                    <div class="step-item">
                        <?= $svgCheck ?>

                        <div class="step-item-text">
                            <div class="avf_txt type-2">
                                <?php _e('Envío',  'avify-wordpress'); ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="step-item">
                    <?= $svgCheck ?>

                    <div class="step-item-text">
                        <div class="avf_txt type-2">
                            <?php _e('Método de pago',  'avify-wordpress'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="main-container-list">
                <div class="main-container-item">
                    <div class="step-content-list">
                        <div class="step-content" data-step="personal">
                            <div class="step-content-title">
                                <div class="avf_txt type-3">
                                    <?php _e('Datos personales',  'avify-wordpress'); ?>
                                </div>
                            </div>

                            <div class="step-content-live-divider"></div>

                            <div class="step-content-personal-details-form">
                                <div class="avf_form">
                                    <div class="avf_form-row avf_form-row-1-of-2">
                                        <div class="avf_form-input">
                                            <div class="avf_form-input-label">
                                                <?php _e('Nombre',  'avify-wordpress'); ?> <span>*</span>
                                            </div>

                                            <div class="avf_form-input-inner">
                                                <input type="text" id="avf_billing_name"/>
                                            </div>
                                        </div>

                                        <div class="avf_form-input">
                                            <div class="avf_form-input-label">
                                                <?php _e('Apellidos',  'avify-wordpress'); ?> <span>*</span>
                                            </div>

                                            <div class="avf_form-input-inner">
                                                <input type="text" id="avf_billing_lastname"/>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="avf_form-row">
                                        <div class="avf_form-input">
                                            <div class="avf_form-input-label">
                                                <?php _e('Correo electrónico',  'avify-wordpress'); ?> <span>*</span>
                                            </div>

                                            <div class="avf_form-input-inner">
                                                <input type="text" id="avf_billing_email">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="avf_form-row">
                                        <div class="avf_form-input">
                                            <div class="avf_form-input-label">
                                                <?php _e('Número de teléfono',  'avify-wordpress'); ?> <span>*</span>
                                            </div>

                                            <div class="avf_form-input-inner">
                                                <input type="text" id="avf_billing_tel">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-content-next-step-button">
                                <div class="avf_btn type-1 var-disabled" id="avf_to_second_step_button">
                                    <div class="avf_btn-inner">
                                        <div class="avf_btn-frame"></div>

                                        <div class="avf_btn-text">
                                            <?php if($cart && $cart->needs_shipping()): ?>
                                                <?php _e('Continuar a envío',  'avify-wordpress'); ?>
                                            <?php else: ?>
                                                <?php _e('Continuar a pago',  'avify-wordpress'); ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if($cart && $cart->needs_shipping()): ?>
                            <div class="step-content" data-step="shipping">
                                <div class="step-content-title">
                                    <div class="avf_txt type-3">
                                        <?php _e('Dirección de entrega',  'avify-wordpress'); ?>
                                    </div>
                                </div>

                                <div class="step-content-live-divider"></div>

                                <div class="step-content-shipping-method-list flex" id="avf_shipping_or_pick">
                                    <div class="step-content-shipping-method-item">
                                        <label class="step-content-shipping-method-item-label">
                                            <input type="radio" name="avf_shipping_method" value="1"
                                                   id="avf_shipping_or_pick_1" checked>

                                            <div class="step-content-shipping-method-item-radio"></div>

                                            <div class="step-content-shipping-method-item-text">
                                                <div class="avf_txt type-4">
                                                    <?php _e('Entrega a domicilio',  'avify-wordpress'); ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <div class="step-content-shipping-method-item">
                                        <label class="step-content-shipping-method-item-label">
                                            <input type="radio" name="avf_shipping_method" value="2"
                                                   id="avf_shipping_or_pick_2">

                                            <div class="step-content-shipping-method-item-radio"></div>

                                            <div class="step-content-shipping-method-item-text">
                                                <div class="avf_txt type-4">
                                                    <?php _e('Pasar a recoger',  'avify-wordpress'); ?>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="step-content-shipping-method-content-list">
                                    <div class="step-content-shipping-method-content-item step-content-shipping-method-content-item-1">
                                        <div class="step-content-shipping-form">
                                            <div class="avf_form">
                                                <div class="avf_form-row">
                                                    <div class="avf_form-input">
                                                        <div class="avf_form-input-label">
                                                            <?php _e('País',  'avify-wordpress'); ?> <span>*</span>
                                                        </div>

                                                        <div class="avf_form-input-inner">
                                                            <input type="text" id="avf_billing_country" value=""
                                                                   readonly="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="avf_form-row avf_form-row-1-of-2">
                                                    <div class="avf_form-input">
                                                        <div class="avf_form-input-label"
                                                             data-text="<?php _e('Provincia',  'avify-wordpress'); ?>"
                                                             data-mx-text="<?php _e('Estado',  'avify-wordpress'); ?>"
                                                        >
                                                            <b><?php _e('Provincia',  'avify-wordpress'); ?></b> <span>*</span>
                                                        </div>

                                                        <div class="avf_form-input-inner">
                                                            <select id="avf_billing_state"></select>
                                                        </div>
                                                    </div>

                                                    <div class="avf_form-input">
                                                        <div class="avf_form-input-label"
                                                             data-text="<?php _e('Cantón',  'avify-wordpress'); ?>"
                                                             data-mx-text="<?php _e('Ciudad',  'avify-wordpress'); ?>"
                                                             data-cr-text="<?php _e('Cantón',  'avify-wordpress'); ?>"
                                                        >
                                                            <b><?php _e('Cantón',  'avify-wordpress'); ?></b> <span>*</span>
                                                        </div>

                                                        <div class="avf_form-input-inner">
                                                            <select id="avf_billing_city"></select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="avf_form-row avf_form-row-1-of-2"
                                                     id="avf_district_and_postal_row">
                                                    <div class="avf_form-input">
                                                        <div class="avf_form-input-label"
                                                             data-text="<?php _e('Distrito',  'avify-wordpress'); ?>"
                                                             data-mx-text="<?php _e('Colonia',  'avify-wordpress'); ?>"
                                                        >
                                                            <b><?php _e('Distrito',  'avify-wordpress'); ?></b>
                                                        </div>

                                                        <div class="avf_form-input-inner">
                                                            <input type="text" id="avf_billing_district">
                                                        </div>
                                                    </div>

                                                    <div class="avf_form-input">
                                                        <div class="avf_form-input-label">
                                                            <?php _e('Código postal',  'avify-wordpress'); ?> <span>*</span>
                                                        </div>

                                                        <div class="avf_form-input-inner">
                                                            <input type="text" id="avf_billing_postal">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="avf_form-row">
                                                    <div class="avf_form-input">
                                                        <div class="avf_form-input-label">
                                                            <?php _e('Dirección exacta',  'avify-wordpress'); ?> <span>*</span>
                                                        </div>

                                                        <div class="avf_form-input-inner">
                                                            <textarea id="avf_billing_address"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="step-content-map">
                                            <div class="step-content-map-label">
                                                <div class="avf_txt type-5">
                                                    <?php _e('Coloque su ubicación en el mapa',  'avify-wordpress'); ?>
                                                </div>
                                            </div>

                                            <div class="step-content-map-text">
                                                <div class="avf_txt type-6">
                                                    <?php _e('Coloque el punto en el mapa para que el repartidor sepa con mayor certeza dónde entregará el paquete.',  'avify-wordpress'); ?>
                                                </div>
                                            </div>

                                            <div class="step-content-map-container" id="avf_map">
                                                <!-- place for google map -->
                                            </div>

                                            <div class="step-content-map-text-2">
                                                <div class="avf_txt type-6">
                                                    <?php _e('Al colocar los datos de envío, se mostrarán las opciones disponibles',  'avify-wordpress'); ?>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="step-content-shipping-var-list" id="avf_shipping_methods">
                                            <div class="step-content-shipping-var-item">
                                                <label class="step-content-shipping-var-item-label">
                                                    <div class="step-content-shipping-var-item-part-1">
                                                        <input type="radio" name="avf_shipping_method_var" value="1">

                                                        <div class="step-content-shipping-var-item-radio"></div>

                                                        <div class="step-content-shipping-var-item-text">
                                                            <div class="avf_txt type-7">
                                                                <!-- place for shipping method name -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="step-content-shipping-var-item-part-2">
                                                        <div class="step-content-shipping-var-item-text-2">
                                                            <div class="avf_txt type-7">
                                                                <!-- place for shipping method price -->
                                                            </div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="step-content-shipping-var-loader" id="avf_shipping_methods_loader">
                                            <div class="avf_img">
                                                <img src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/loading.gif" alt="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="step-content-shipping-method-content-item step-content-shipping-method-content-item-2">
                                        <div class="step-content-self-pickup-text">
                                            <div class="avf_txt type-8">
                                                <?php _e('Anote esta dirección o revise su correo al finalizar la orden para pasar a recoger',  'avify-wordpress'); ?>
                                            </div>
                                        </div>

                                        <div class="step-content-self-pickup-info"></div>
                                    </div>
                                </div>

                                <div class="step-content-next-step-button">
                                    <div class="avf_btn type-1 var-disabled" id="avf_to_third_step_button">
                                        <div class="avf_btn-inner">
                                            <div class="avf_btn-frame"></div>

                                            <div class="avf_btn-text">
                                                <?php _e('Continuar a pago',  'avify-wordpress'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="step-content" data-step="payment">
                            <div class="step-content-title">
                                <div class="avf_txt type-3">
                                    <?php _e('Método de pago',  'avify-wordpress'); ?>
                                </div>
                            </div>
                            <div class="step-content-live-divider"></div>
                            <div class="step-content-payment-container-for-woo">
                                <!-- here goes payments from woo -->
                            </div>

                            <div class="step-content-billing-address">
                                <div class="step-content-avf-billing-summary">
                                    <div class="avf_txt type-8">
                                        <?php _e('Dirección de facturación',  'avify-wordpress'); ?>
                                    </div>
                                    <div class="avf_txt type-6" id="avf_billing_summary"></div>
                                </div>
                                <div class="step-content-avf-dif-billing-checkbox">
                                    <div class="step-content-avf-dif-billing-custom-checkbox">
                                        <div class="step-content-avf-dif-billing-custom-checkbox-checked-icon">
                                            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                 xmlns="http://www.w3.org/2000/svg">
                                                <path d="M1.75 0C1.28587 0 0.840752 0.184374 0.512563 0.512563C0.184374 0.840752 0 1.28587 0 1.75L0 12.25C0 12.7141 0.184374 13.1592 0.512563 13.4874C0.840752 13.8156 1.28587 14 1.75 14H12.25C12.7141 14 13.1592 13.8156 13.4874 13.4874C13.8156 13.1592 14 12.7141 14 12.25V1.75C14 1.28587 13.8156 0.840752 13.4874 0.512563C13.1592 0.184374 12.7141 0 12.25 0L1.75 0ZM10.5262 4.34875C10.6478 4.47016 10.7169 4.63438 10.7187 4.80616C10.7205 4.97794 10.6548 5.14357 10.5359 5.2675L7.04287 9.63375C6.98284 9.69841 6.91039 9.7503 6.82984 9.78632C6.74929 9.82234 6.66231 9.84175 6.57409 9.84338C6.48588 9.84501 6.39824 9.82884 6.31641 9.79583C6.23459 9.76282 6.16026 9.71364 6.09787 9.65125L3.7835 7.336C3.71902 7.27592 3.66731 7.20347 3.63144 7.12297C3.59557 7.04247 3.57629 6.95557 3.57473 6.86746C3.57318 6.77934 3.58939 6.69182 3.62239 6.6101C3.6554 6.52839 3.70452 6.45416 3.76684 6.39184C3.82916 6.32952 3.90339 6.2804 3.9851 6.24739C4.06682 6.21439 4.15434 6.19818 4.24246 6.19973C4.33057 6.20129 4.41747 6.22057 4.49797 6.25644C4.57847 6.29231 4.65092 6.34402 4.711 6.4085L6.54325 8.23987L9.58212 4.368C9.64204 4.30322 9.7144 4.25119 9.79488 4.21502C9.87536 4.17885 9.96231 4.15928 10.0505 4.15749C10.1388 4.15569 10.2264 4.1717 10.3083 4.20456C10.3902 4.23743 10.4646 4.28647 10.5271 4.34875H10.5262Z"
                                                      fill="#525245"/>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="step-content-avf-dif-billing-text">
                                        <div class="avf_txt type-7">
                                            <?php _e('Agregar dirección de facturación diferente',  'avify-wordpress'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="step-content-avf-dif-billing-form">
                                    <div class="step-content-billing-form">
                                        <div class="avf_form">
                                            <div class="avf_form-row avf_form-row-1-of-2">
                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('Nombre',  'avify-wordpress'); ?> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <input type="text" id="avf_dif_billing_name"/>
                                                    </div>
                                                </div>

                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('Apellidos',  'avify-wordpress'); ?> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <input type="text" id="avf_dif_billing_lastname"/>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="avf_form-row">
                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('País',  'avify-wordpress'); ?> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <input type="text" id="avf_dif_billing_country" value=""
                                                               readonly="">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="avf_form-row avf_form-row-1-of-2">
                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label"
                                                         data-text="<?php _e('Provincia',  'avify-wordpress'); ?>"
                                                         data-mx-text="<?php _e('Estado',  'avify-wordpress'); ?>"
                                                    >
                                                        <b><?php _e('Provincia',  'avify-wordpress'); ?></b> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <select id="avf_dif_billing_state">

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label"
                                                         data-text="<?php _e('Cantón',  'avify-wordpress'); ?>"
                                                         data-mx-text="<?php _e('Ciudad',  'avify-wordpress'); ?>"
                                                         data-cr-text="<?php _e('Cantón',  'avify-wordpress'); ?>"
                                                    >
                                                        <b><?php _e('Cantón',  'avify-wordpress'); ?></b> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <select id="avf_dif_billing_city"></select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="avf_form-row avf_form-row-1-of-2"
                                                 id="avf_dif_district_and_postal_row">
                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label"
                                                         data-text="<?php _e('Distrito',  'avify-wordpress'); ?>"
                                                         data-mx-text="<?php _e('Colonia',  'avify-wordpress'); ?>"
                                                    >
                                                        <b><?php _e('Distrito',  'avify-wordpress'); ?></b>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <input type="text" id="avf_dif_billing_district">
                                                    </div>
                                                </div>

                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('Código postal',  'avify-wordpress'); ?> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <input type="text" id="avf_dif_billing_postal">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="avf_form-row">
                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('Dirección exacta',  'avify-wordpress'); ?> <span>*</span>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <textarea id="avf_dif_billing_address"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-content-electronic-invoice">
                                <div class="step-content-avf-electronic-invoice">
                                    <div class="step-content-avf-electronic-invoice-label">
                                        <div class="avf-electronic-invoice-checkbox">
                                            <div class="avf-electronic-invoice-checkbox-checked-icon">
                                                <svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                                                     xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1.75 0C1.28587 0 0.840752 0.184374 0.512563 0.512563C0.184374 0.840752 0 1.28587 0 1.75L0 12.25C0 12.7141 0.184374 13.1592 0.512563 13.4874C0.840752 13.8156 1.28587 14 1.75 14H12.25C12.7141 14 13.1592 13.8156 13.4874 13.4874C13.8156 13.1592 14 12.7141 14 12.25V1.75C14 1.28587 13.8156 0.840752 13.4874 0.512563C13.1592 0.184374 12.7141 0 12.25 0L1.75 0ZM10.5262 4.34875C10.6478 4.47016 10.7169 4.63438 10.7187 4.80616C10.7205 4.97794 10.6548 5.14357 10.5359 5.2675L7.04287 9.63375C6.98284 9.69841 6.91039 9.7503 6.82984 9.78632C6.74929 9.82234 6.66231 9.84175 6.57409 9.84338C6.48588 9.84501 6.39824 9.82884 6.31641 9.79583C6.23459 9.76282 6.16026 9.71364 6.09787 9.65125L3.7835 7.336C3.71902 7.27592 3.66731 7.20347 3.63144 7.12297C3.59557 7.04247 3.57629 6.95557 3.57473 6.86746C3.57318 6.77934 3.58939 6.69182 3.62239 6.6101C3.6554 6.52839 3.70452 6.45416 3.76684 6.39184C3.82916 6.32952 3.90339 6.2804 3.9851 6.24739C4.06682 6.21439 4.15434 6.19818 4.24246 6.19973C4.33057 6.20129 4.41747 6.22057 4.49797 6.25644C4.57847 6.29231 4.65092 6.34402 4.711 6.4085L6.54325 8.23987L9.58212 4.368C9.64204 4.30322 9.7144 4.25119 9.79488 4.21502C9.87536 4.17885 9.96231 4.15928 10.0505 4.15749C10.1388 4.15569 10.2264 4.1717 10.3083 4.20456C10.3902 4.23743 10.4646 4.28647 10.5271 4.34875H10.5262Z"
                                                          fill="#525245"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="avf-electronic-invoice-label">
                                            <div class="avf_txt type-7">
                                                <?php _e('Deseo factura electrónica',  'avify-wordpress'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="step-content-avf-electronic-invoice-holder">
                                    <div class="step-content-avf-electronic-form-container">
                                        <div class="avf_form">
                                            <div class="avf_form-row avf_form-row-1-of-2">
                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('Tipo de identificación',  'avify-wordpress'); ?>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <select id="avf_additional_identification_type">

                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="avf_form-input">
                                                    <div class="avf_form-input-label">
                                                        <?php _e('Número de identificación',  'avify-wordpress'); ?>
                                                    </div>

                                                    <div class="avf_form-input-inner">
                                                        <input type="text" id="avf_additional_identification_number">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="step-content-next-step-button">
                                <div class="avf_btn type-1" id="avf_checkout_button">
                                    <div class="avf_btn-inner">
                                        <div class="avf_btn-frame"></div>
                                        <div class="avf_btn-text">
                                            <?php _e('Realizar Pedido',  'avify-wordpress'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="main-container-item">
                    <button id="avf-open-order-summary">
                        <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7.00003 15.5C6.59557 15.5 6.23093 15.2564 6.07615 14.8827C5.92137 14.509 6.00692 14.0789 6.29292 13.7929L11.2929 8.79289C11.6834 8.40237 12.3166 8.40237 12.7071 8.79289L17.7071 13.7929C17.9931 14.0789 18.0787 14.509 17.9239 14.8827C17.7691 15.2564 17.4045 15.5 17 15.5H7.00003Z" fill="#000000"/>
                        </svg>
                    </button>
                    <div class="review-order">
                        <div class="review-order-title">
                            <div class="avf_txt type-4">
                                <?php _e('Resumen de la orden',  'avify-wordpress'); ?>
                            </div>
                        </div>

                        <div class="review-order-product-list">
                            <?php
                            if ($cart) {
                                foreach ($cart->get_cart() ?? [] as $cart_item) {
                                    $product = $cart_item['data'];
                                    $quantity = $cart_item['quantity'];

                                    if (!empty($product)) {
                                        ?>
                                        <div class="review-order-product-item flex middle space-between">
                                            <div class="review-order-product-item-part flex middle">
                                                <div class="review-order-product-item-image">
                                                    <?= $product->get_image(); ?>
                                                </div>

                                                <div class="review-order-product-item-name">
                                                    <div class="avf_txt type-9">
                                                        <?= $product->get_name(); ?> x <?= $quantity; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="review-order-product-item-price"
                                                 data-item-quantity="<?= $quantity; ?>">
                                                <div class="avf_txt type-9">
                                                    <?= get_woocommerce_currency_symbol(); ?>
                                                    <?= $product->get_price(); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>

                        <div class="review-order-total">
                            <div class="review-order-total-subtotal review-order-total-part-container">
                                <div class="review-order-total-left-part">
                                    <div class="avf_txt type-7">
                                        <?php _e('Subtotal',  'avify-wordpress'); ?>
                                    </div>
                                </div>

                                <div class="review-order-total-right-part">
                                    <div class="avf_txt type-7" id="avf_subtotal">
                                        0
                                    </div>
                                </div>
                            </div>

                            <div class="review-order-total-shipping review-order-total-part-container">
                                <div class="review-order-total-left-part">
                                    <div class="avf_txt type-10">
                                        <?php _e('Envío',  'avify-wordpress'); ?>
                                    </div>
                                </div>

                                <div class="review-order-total-right-part">
                                    <div class="avf_txt type-10" id="avf_shipping_selected"></div>
                                </div>
                            </div>

                            <div class="review-order-total-discount review-order-total-part-container">
                                <div class="review-order-total-left-part">
                                    <div class="avf_txt type-10">
                                        <?php _e('Descuento',  'avify-wordpress'); ?>
                                    </div>
                                </div>

                                <div class="review-order-total-right-part">
                                    <div class="avf_txt type-10" id="avf_discount">
                                        0
                                    </div>
                                </div>
                            </div>

                            <div class="review-order-total-taxes review-order-total-part-container">
                                <div class="review-order-total-left-part">
                                    <div class="avf_txt type-10">
                                        <?php _e('Impuestos',  'avify-wordpress'); ?>
                                    </div>
                                </div>

                                <div class="review-order-total-right-part">
                                    <div class="avf_txt type-10" id="avf_taxes">
                                        0
                                    </div>
                                </div>
                            </div>

                            <div class="review-order-total-total review-order-total-part-container">
                                <div class="review-order-total-left-part">
                                    <div class="avf_txt type-4">
                                        <?php _e('Total',  'avify-wordpress'); ?>
                                    </div>
                                </div>

                                <div class="review-order-total-right-part">
                                    <div class="avf_txt type-4" id="avf_total">
                                        0
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a class="powered-by-avify"
                           href="https://avify.com/?utm_campaign=poweredby&utm_medium=checkout&utm_source=onlinestore"
                           target="_blank">
                            <div class="powered-by-avify-inner flex middle center">
                                <div class="powered-by-avify-text flex middle">
                                    <div class="avf_txt type-10">
                                        Powered by
                                    </div>
                                </div>

                                <div class="powered-by-avify-icon">
                                    <svg width="1327" height="405" viewBox="0 0 1327 405" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M779.794 244.672L728.528 102.263L727.694 99.9355H666.795L746.651 315.218H812.6L892.017 99.9355H831.133L779.794 244.672Z"
                                              fill="#271744"/>
                                        <path d="M933.768 4.48828C924.223 4.48828 916.055 7.75279 909.482 14.1793C902.88 20.6352 899.527 28.6135 899.527 37.9093C899.527 47.4686 902.865 55.6518 909.453 62.2248C916.026 68.8124 924.209 72.1501 933.768 72.1501C943.049 72.1501 951.101 68.8124 957.688 62.2248C964.276 55.6518 967.614 47.4686 967.614 37.9093C967.614 28.6135 964.261 20.6352 957.659 14.1793C951.072 7.75279 943.035 4.48828 933.768 4.48828Z"
                                              fill="#271744"/>
                                        <path d="M964.789 99.9355H903.173V315.218H964.789V99.9355Z" fill="#271744"/>
                                        <path d="M986.19 66.2518C986.19 51.6274 989.03 39.6966 994.608 30.796C1000.19 21.9979 1007.27 15.1908 1015.72 10.5941C1023.99 6.0999 1033.01 3.12817 1042.55 1.72282C1051.95 0.361384 1060.72 -0.209547 1068.68 0.0685957C1076.6 0.346738 1086.04 1.43005 1096.67 3.33313L1099.97 3.93329L1093.44 55.5946L1089.97 55.17C1087.73 54.8919 1085.17 54.4674 1082.43 53.9111C1080.57 53.7062 1077.98 53.4134 1074.92 53.1792C1071.87 52.901 1068.78 52.7546 1065.72 52.7546C1061.03 52.7546 1057.48 53.4866 1055.24 54.8626C1052.96 56.2973 1051.25 58.0247 1050.22 59.9863C1049.09 62.1236 1048.39 64.6123 1048.19 67.3498C1047.91 70.4679 1047.81 73.4836 1047.81 76.3675V99.9657H1095.3L1088.14 146.62H1047.81V315.263H986.176V146.62V66.2518H986.19Z"
                                              fill="#271744"/>
                                        <path d="M1176.54 127.34C1190.55 169.032 1215.6 243.589 1215.6 243.589L1259.27 99.9355H1326.3L1247.53 320.019L1246.72 322.259C1244.66 328.042 1242.2 333.663 1239.36 339.109C1238.19 341.348 1236.86 343.735 1235.38 346.253C1231.81 352.299 1227.21 358.769 1221.73 365.488C1216.16 372.296 1209.35 378.693 1201.47 384.49C1193.58 390.302 1184.32 395.22 1173.94 399.114C1163.47 403.008 1151.55 404.999 1138.54 404.999H1135.03V351.479H1138.54C1145.68 351.479 1151.88 349.942 1156.97 346.926C1162.33 343.735 1166.82 340.192 1170.35 336.415C1174.07 332.228 1177.33 327.383 1180.03 322.025L1099.98 99.9355H1167.35C1167.33 99.9355 1171.11 111.164 1176.54 127.34Z"
                                              fill="#271744"/>
                                        <path d="M665.99 142.666C665.361 134.044 663.326 126.461 659.93 120.108C656.314 113.344 650.561 108.045 642.817 104.327C635.351 100.755 625.381 99.0273 612.309 99.0273H476.941V147.819H600.788C605.663 147.819 609.703 148.449 612.748 149.781C613.275 149.986 614.636 151.991 614.753 158.198C607.595 159.384 599.704 160.76 591.287 162.297C580.659 164.23 569.607 166.821 558.496 170.012C547.414 173.204 536.405 177.097 525.806 181.518C514.974 186.115 505.18 191.766 496.733 198.353C488.111 205.087 481.055 212.978 475.755 221.776C470.28 230.896 467.514 241.627 467.514 253.631C467.514 264.507 469.9 274.33 474.628 282.879C479.254 291.224 485.461 298.412 493.074 304.238C500.613 310.02 509.279 314.412 518.78 317.355C528.251 320.224 538.03 321.702 547.897 321.702C558.671 321.702 568.772 319.99 577.922 316.623C586.895 313.285 594.961 308.908 601.871 303.652C606.643 299.963 610.845 295.908 614.358 291.517V315.232H639.611H666.795L666.825 245.257V168.753C666.839 159.37 666.547 150.601 665.99 142.666ZM609.103 234.673C605.385 240.734 600.51 246.311 594.61 251.186C588.681 256.09 581.801 260.203 574.233 263.395C566.825 266.44 559.359 268.021 551.966 268.021C548.599 268.021 544.984 267.596 541.236 266.718C537.591 265.913 534.297 264.683 531.384 263.073C528.72 261.565 526.436 259.603 524.65 257.29C523.171 255.358 522.44 253.118 522.44 250.381C522.44 246.414 523.874 242.842 526.86 239.431C530.374 235.434 535.029 231.716 540.753 228.378C546.682 224.909 553.445 221.747 560.882 219.009C568.421 216.199 576.062 213.71 583.616 211.543C591.082 209.435 598.27 207.576 605.018 206.112C608.459 205.38 611.577 204.707 614.388 204.107V217.018V218.38C614.358 223.474 612.572 228.993 609.103 234.673Z"
                                              fill="#271744"/>
                                        <path d="M357.999 147.762L356.315 146.898L355.583 148.64C351.923 157.292 347.488 166.31 342.379 175.444L341.486 177.025L343.111 177.86C361.732 187.404 368.363 196.847 368.363 202.497C368.363 208.163 361.732 217.62 343.111 227.179C339.612 228.979 335.747 230.736 331.59 232.434C328.003 226.227 324.153 220.05 320.142 214.048L318.751 211.969L317.273 213.989C310.992 222.538 304.346 230.927 297.539 238.949L296.719 239.915L297.393 240.998C322.865 282.383 327.725 312.51 319.366 320.854C311.007 329.213 280.88 324.338 239.495 298.881C236.07 296.788 232.483 294.475 228.809 292.001C231.824 289.468 234.972 286.745 238.368 283.715C245.98 276.996 253.578 269.852 260.971 262.459C268.378 255.052 275.522 247.454 282.227 239.871C285.828 235.845 289.019 232.127 292.006 228.511C298.769 220.43 305.313 212.027 311.461 203.507C314.55 199.218 317.404 195.105 320.157 190.947C324.46 184.506 328.574 177.874 332.366 171.228C337.723 161.772 342.276 152.607 345.877 144C361.512 106.729 360.575 77.8018 343.125 60.3666C325.954 43.195 297.612 42.0239 261.117 56.9411C259.492 53.091 257.692 49.2409 255.774 45.4787C241.003 16.6983 222.075 1.48828 201.009 1.48828C179.929 1.48828 161.001 16.6983 146.274 45.4787L145.41 47.1622L147.152 47.8942C155.804 51.5539 164.821 55.9896 173.956 61.0986L175.537 61.9769L176.372 60.3666C185.931 41.7457 195.358 35.1142 201.009 35.1142C206.674 35.1142 216.131 41.7457 225.691 60.3666C227.491 63.88 229.263 67.7594 230.946 71.8876C224.739 75.4888 218.547 79.3242 212.559 83.3353L210.481 84.726L212.501 86.2046C221.021 92.4701 229.424 99.1016 237.46 105.938L238.427 106.758L239.51 106.084C280.88 80.6271 311.007 75.7816 319.366 84.1405C327.71 92.4848 322.85 122.597 297.393 163.967C295.27 167.422 293.016 170.936 290.512 174.654C287.98 171.638 285.257 168.491 282.227 165.095C275.522 157.497 268.363 149.899 260.971 142.492C253.563 135.084 245.966 127.941 238.383 121.236C234.313 117.605 230.595 114.399 227.023 111.457C218.942 104.694 210.539 98.1501 202.019 92.0017C197.701 88.8836 193.587 86.0436 189.459 83.3061C183.032 79.0168 176.401 74.9032 169.74 71.0971C160.444 65.8124 151.295 61.2596 142.541 57.5852C105.24 41.9507 76.3135 42.9022 58.8784 60.3374C41.6921 77.5236 40.521 105.865 55.4528 142.36C51.5149 144.044 47.6648 145.844 43.9904 147.733C15.21 162.533 0 181.461 0 202.497C0 223.578 15.21 242.506 43.9904 257.233L45.6739 258.082L46.4059 256.355C50.1388 247.556 54.5745 238.553 59.6103 229.594L60.5033 227.999L58.8784 227.164C40.2575 217.62 33.626 208.163 33.626 202.483C33.626 196.832 40.2575 187.39 58.8784 177.831C62.3185 176.045 66.1833 174.259 70.3847 172.546C73.9859 178.767 77.8213 184.945 81.8324 190.932L83.2231 193.011L84.7017 190.991C90.9818 182.457 97.6133 174.054 104.435 166.017L105.255 165.051L104.582 163.967C79.1242 122.627 74.2787 92.5141 82.6376 84.1405C91.0111 75.767 121.124 80.6125 162.464 106.084C165.875 108.178 169.462 110.491 173.151 112.965C170.135 115.483 167.003 118.206 163.592 121.251C156.023 127.926 148.411 135.084 140.989 142.507C133.582 149.914 126.438 157.512 119.733 165.095C116.088 169.179 112.897 172.897 109.954 176.455C103.22 184.506 96.6765 192.909 90.4988 201.458C87.4246 205.733 84.5699 209.846 81.8032 214.018C77.5139 220.445 73.4003 227.076 69.5941 233.737C64.2362 243.194 59.6835 252.358 56.0823 260.966C40.4478 298.252 41.3993 327.178 58.8344 344.599C76.0207 361.785 104.362 362.956 140.872 348.024C142.497 351.86 144.283 355.71 146.215 359.487C160.942 388.267 179.87 403.477 200.951 403.477C213.628 403.477 225.53 398.002 236.319 387.199C243.389 380.128 249.904 370.803 255.686 359.487L256.535 357.803L254.808 357.071C246.01 353.338 237.007 348.903 228.047 343.867L226.466 342.974L225.632 344.599C216.087 363.205 206.631 369.837 200.951 369.837C195.285 369.837 185.843 363.205 176.298 344.584C174.512 341.144 172.726 337.279 171.014 333.078C177.235 329.477 183.413 325.627 189.4 321.63L191.479 320.239L189.459 318.761C180.924 312.495 172.521 305.849 164.499 299.027L163.533 298.208L162.45 298.881C121.08 324.338 90.9818 329.199 82.6229 320.854C74.2494 312.481 79.1096 282.368 104.552 240.998C106.792 237.368 109.105 233.781 111.433 230.312C113.994 233.357 116.717 236.504 119.718 239.871C126.423 247.469 133.582 255.066 140.974 262.474C148.367 269.866 155.965 277.01 163.562 283.73C167.647 287.389 171.365 290.581 174.922 293.508C182.959 300.242 191.377 306.786 199.926 312.964C204.23 316.053 208.343 318.907 212.486 321.659C218.898 325.949 225.53 330.048 232.205 333.868C241.676 339.241 250.826 343.779 259.434 347.38C296.719 363.015 325.646 362.063 343.067 344.628C360.238 327.457 361.424 299.115 346.492 262.62C350.445 260.951 354.295 259.151 357.94 257.248C369.271 251.48 378.61 244.965 385.696 237.88C396.47 227.106 401.945 215.219 401.945 202.541C401.989 181.461 386.779 162.533 357.999 147.762ZM259.873 202.497C259.873 234.967 233.464 261.376 200.994 261.376C168.525 261.376 142.116 234.967 142.116 202.497C142.116 170.028 168.525 143.619 200.994 143.619C233.464 143.619 259.873 170.043 259.873 202.497Z"
                                              fill="#271744"/>
                                    </svg>
                                </div>
                            </div>
                        </a>

                        <div class="avf-checkout-loader">
                            <div class="avf_img">
                                <img src="<?= plugin_dir_url( __FILE__ ) ?>../assets/img/loading.gif" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="type-avify-success">
    <div class="wrapper">
        <div class="content">
            <div class="el-icon">
                <div class="avf_img">
                    <svg width="77" height="77" viewBox="0 0 77 77" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <g clip-path="url(#clip0_60_3701)">
                            <path d="M52.7924 23.9181C52.7582 23.9514 52.726 23.9867 52.6962 24.024L35.9824 45.3193L25.9098 35.2419C25.2256 34.6044 24.3206 34.2573 23.3855 34.2738C22.4505 34.2903 21.5583 34.6691 20.897 35.3304C20.2357 35.9917 19.8569 36.8839 19.8404 37.8189C19.8239 38.754 20.171 39.659 20.8086 40.3432L33.5424 53.0819C33.8855 53.4243 34.294 53.6941 34.7436 53.8752C35.1931 54.0564 35.6746 54.1451 36.1592 54.1361C36.6438 54.1271 37.1217 54.0206 37.5642 53.823C38.0068 53.6253 38.405 53.3405 38.7351 52.9856L57.9466 28.9713C58.6008 28.2847 58.9585 27.3682 58.9423 26.42C58.9261 25.4718 58.5374 24.5681 57.8603 23.9041C57.1831 23.2402 56.2718 22.8695 55.3235 22.8721C54.3752 22.8748 53.466 23.2505 52.7924 23.9181V23.9181Z"
                                  fill="#6BDB98"/>
                            <circle opacity="0.1" cx="38.5" cy="38.5" r="38.5" fill="#6BDB98"/>
                        </g>
                        <defs>
                            <clipPath id="clip0_60_3701">
                                <rect width="77" height="77" fill="white"/>
                            </clipPath>
                        </defs>
                    </svg>
                </div>
            </div>

            <div class="el-title">
                <div class="avf_txt type-11">
                    <?php _e('¡Compra finalizada!',  'avify-wordpress'); ?>
                </div>
            </div>

            <div class="el-text">
                <div class="avf_txt type-12">
                    <?php _e('Su orden ha sido tramitada de forma exitosa.',  'avify-wordpress'); ?>
                    <?php _e('Hemos enviado un correo con la confirmación de su pedido',  'avify-wordpress'); ?>
                    <span id="avf_order_number">#0000</span>
                </div>
            </div>

            <div class="el-button-1">
                <a class="avf_btn type-1" href="/">
                    <div class="avf_btn-inner">
                        <div class="avf_btn-frame"></div>

                        <div class="avf_btn-text">
                            <?php _e('Volver a la tienda',  'avify-wordpress'); ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="el-button-2">
                <a class="avf_btn type-1 var-a" id="avf_register_after_checkout" href="#">
                    <div class="avf_btn-inner">
                        <div class="avf_btn-frame"></div>

                        <div class="avf_btn-text">
                            <?php _e('Crear una cuenta',  'avify-wordpress'); ?>
                        </div>
                    </div>
                </a>
            </div>

            <div class="el-powered-by">
                <a class="powered-by-avify"
                   href="https://avify.com/?utm_campaign=poweredby&utm_medium=checkout&utm_source=onlinestore"
                   target="_blank">
                    <div class="powered-by-avify-inner flex middle center">
                        <div class="powered-by-avify-text flex middle">
                            <div class="avf_txt type-10">
                                Powered by
                            </div>
                        </div>

                        <div class="powered-by-avify-icon">
                            <svg width="1327" height="405" viewBox="0 0 1327 405" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M779.794 244.672L728.528 102.263L727.694 99.9355H666.795L746.651 315.218H812.6L892.017 99.9355H831.133L779.794 244.672Z"
                                      fill="#271744"></path>
                                <path d="M933.768 4.48828C924.223 4.48828 916.055 7.75279 909.482 14.1793C902.88 20.6352 899.527 28.6135 899.527 37.9093C899.527 47.4686 902.865 55.6518 909.453 62.2248C916.026 68.8124 924.209 72.1501 933.768 72.1501C943.049 72.1501 951.101 68.8124 957.688 62.2248C964.276 55.6518 967.614 47.4686 967.614 37.9093C967.614 28.6135 964.261 20.6352 957.659 14.1793C951.072 7.75279 943.035 4.48828 933.768 4.48828Z"
                                      fill="#271744"></path>
                                <path d="M964.789 99.9355H903.173V315.218H964.789V99.9355Z" fill="#271744"></path>
                                <path d="M986.19 66.2518C986.19 51.6274 989.03 39.6966 994.608 30.796C1000.19 21.9979 1007.27 15.1908 1015.72 10.5941C1023.99 6.0999 1033.01 3.12817 1042.55 1.72282C1051.95 0.361384 1060.72 -0.209547 1068.68 0.0685957C1076.6 0.346738 1086.04 1.43005 1096.67 3.33313L1099.97 3.93329L1093.44 55.5946L1089.97 55.17C1087.73 54.8919 1085.17 54.4674 1082.43 53.9111C1080.57 53.7062 1077.98 53.4134 1074.92 53.1792C1071.87 52.901 1068.78 52.7546 1065.72 52.7546C1061.03 52.7546 1057.48 53.4866 1055.24 54.8626C1052.96 56.2973 1051.25 58.0247 1050.22 59.9863C1049.09 62.1236 1048.39 64.6123 1048.19 67.3498C1047.91 70.4679 1047.81 73.4836 1047.81 76.3675V99.9657H1095.3L1088.14 146.62H1047.81V315.263H986.176V146.62V66.2518H986.19Z"
                                      fill="#271744"></path>
                                <path d="M1176.54 127.34C1190.55 169.032 1215.6 243.589 1215.6 243.589L1259.27 99.9355H1326.3L1247.53 320.019L1246.72 322.259C1244.66 328.042 1242.2 333.663 1239.36 339.109C1238.19 341.348 1236.86 343.735 1235.38 346.253C1231.81 352.299 1227.21 358.769 1221.73 365.488C1216.16 372.296 1209.35 378.693 1201.47 384.49C1193.58 390.302 1184.32 395.22 1173.94 399.114C1163.47 403.008 1151.55 404.999 1138.54 404.999H1135.03V351.479H1138.54C1145.68 351.479 1151.88 349.942 1156.97 346.926C1162.33 343.735 1166.82 340.192 1170.35 336.415C1174.07 332.228 1177.33 327.383 1180.03 322.025L1099.98 99.9355H1167.35C1167.33 99.9355 1171.11 111.164 1176.54 127.34Z"
                                      fill="#271744"></path>
                                <path d="M665.99 142.666C665.361 134.044 663.326 126.461 659.93 120.108C656.314 113.344 650.561 108.045 642.817 104.327C635.351 100.755 625.381 99.0273 612.309 99.0273H476.941V147.819H600.788C605.663 147.819 609.703 148.449 612.748 149.781C613.275 149.986 614.636 151.991 614.753 158.198C607.595 159.384 599.704 160.76 591.287 162.297C580.659 164.23 569.607 166.821 558.496 170.012C547.414 173.204 536.405 177.097 525.806 181.518C514.974 186.115 505.18 191.766 496.733 198.353C488.111 205.087 481.055 212.978 475.755 221.776C470.28 230.896 467.514 241.627 467.514 253.631C467.514 264.507 469.9 274.33 474.628 282.879C479.254 291.224 485.461 298.412 493.074 304.238C500.613 310.02 509.279 314.412 518.78 317.355C528.251 320.224 538.03 321.702 547.897 321.702C558.671 321.702 568.772 319.99 577.922 316.623C586.895 313.285 594.961 308.908 601.871 303.652C606.643 299.963 610.845 295.908 614.358 291.517V315.232H639.611H666.795L666.825 245.257V168.753C666.839 159.37 666.547 150.601 665.99 142.666ZM609.103 234.673C605.385 240.734 600.51 246.311 594.61 251.186C588.681 256.09 581.801 260.203 574.233 263.395C566.825 266.44 559.359 268.021 551.966 268.021C548.599 268.021 544.984 267.596 541.236 266.718C537.591 265.913 534.297 264.683 531.384 263.073C528.72 261.565 526.436 259.603 524.65 257.29C523.171 255.358 522.44 253.118 522.44 250.381C522.44 246.414 523.874 242.842 526.86 239.431C530.374 235.434 535.029 231.716 540.753 228.378C546.682 224.909 553.445 221.747 560.882 219.009C568.421 216.199 576.062 213.71 583.616 211.543C591.082 209.435 598.27 207.576 605.018 206.112C608.459 205.38 611.577 204.707 614.388 204.107V217.018V218.38C614.358 223.474 612.572 228.993 609.103 234.673Z"
                                      fill="#271744"></path>
                                <path d="M357.999 147.762L356.315 146.898L355.583 148.64C351.923 157.292 347.488 166.31 342.379 175.444L341.486 177.025L343.111 177.86C361.732 187.404 368.363 196.847 368.363 202.497C368.363 208.163 361.732 217.62 343.111 227.179C339.612 228.979 335.747 230.736 331.59 232.434C328.003 226.227 324.153 220.05 320.142 214.048L318.751 211.969L317.273 213.989C310.992 222.538 304.346 230.927 297.539 238.949L296.719 239.915L297.393 240.998C322.865 282.383 327.725 312.51 319.366 320.854C311.007 329.213 280.88 324.338 239.495 298.881C236.07 296.788 232.483 294.475 228.809 292.001C231.824 289.468 234.972 286.745 238.368 283.715C245.98 276.996 253.578 269.852 260.971 262.459C268.378 255.052 275.522 247.454 282.227 239.871C285.828 235.845 289.019 232.127 292.006 228.511C298.769 220.43 305.313 212.027 311.461 203.507C314.55 199.218 317.404 195.105 320.157 190.947C324.46 184.506 328.574 177.874 332.366 171.228C337.723 161.772 342.276 152.607 345.877 144C361.512 106.729 360.575 77.8018 343.125 60.3666C325.954 43.195 297.612 42.0239 261.117 56.9411C259.492 53.091 257.692 49.2409 255.774 45.4787C241.003 16.6983 222.075 1.48828 201.009 1.48828C179.929 1.48828 161.001 16.6983 146.274 45.4787L145.41 47.1622L147.152 47.8942C155.804 51.5539 164.821 55.9896 173.956 61.0986L175.537 61.9769L176.372 60.3666C185.931 41.7457 195.358 35.1142 201.009 35.1142C206.674 35.1142 216.131 41.7457 225.691 60.3666C227.491 63.88 229.263 67.7594 230.946 71.8876C224.739 75.4888 218.547 79.3242 212.559 83.3353L210.481 84.726L212.501 86.2046C221.021 92.4701 229.424 99.1016 237.46 105.938L238.427 106.758L239.51 106.084C280.88 80.6271 311.007 75.7816 319.366 84.1405C327.71 92.4848 322.85 122.597 297.393 163.967C295.27 167.422 293.016 170.936 290.512 174.654C287.98 171.638 285.257 168.491 282.227 165.095C275.522 157.497 268.363 149.899 260.971 142.492C253.563 135.084 245.966 127.941 238.383 121.236C234.313 117.605 230.595 114.399 227.023 111.457C218.942 104.694 210.539 98.1501 202.019 92.0017C197.701 88.8836 193.587 86.0436 189.459 83.3061C183.032 79.0168 176.401 74.9032 169.74 71.0971C160.444 65.8124 151.295 61.2596 142.541 57.5852C105.24 41.9507 76.3135 42.9022 58.8784 60.3374C41.6921 77.5236 40.521 105.865 55.4528 142.36C51.5149 144.044 47.6648 145.844 43.9904 147.733C15.21 162.533 0 181.461 0 202.497C0 223.578 15.21 242.506 43.9904 257.233L45.6739 258.082L46.4059 256.355C50.1388 247.556 54.5745 238.553 59.6103 229.594L60.5033 227.999L58.8784 227.164C40.2575 217.62 33.626 208.163 33.626 202.483C33.626 196.832 40.2575 187.39 58.8784 177.831C62.3185 176.045 66.1833 174.259 70.3847 172.546C73.9859 178.767 77.8213 184.945 81.8324 190.932L83.2231 193.011L84.7017 190.991C90.9818 182.457 97.6133 174.054 104.435 166.017L105.255 165.051L104.582 163.967C79.1242 122.627 74.2787 92.5141 82.6376 84.1405C91.0111 75.767 121.124 80.6125 162.464 106.084C165.875 108.178 169.462 110.491 173.151 112.965C170.135 115.483 167.003 118.206 163.592 121.251C156.023 127.926 148.411 135.084 140.989 142.507C133.582 149.914 126.438 157.512 119.733 165.095C116.088 169.179 112.897 172.897 109.954 176.455C103.22 184.506 96.6765 192.909 90.4988 201.458C87.4246 205.733 84.5699 209.846 81.8032 214.018C77.5139 220.445 73.4003 227.076 69.5941 233.737C64.2362 243.194 59.6835 252.358 56.0823 260.966C40.4478 298.252 41.3993 327.178 58.8344 344.599C76.0207 361.785 104.362 362.956 140.872 348.024C142.497 351.86 144.283 355.71 146.215 359.487C160.942 388.267 179.87 403.477 200.951 403.477C213.628 403.477 225.53 398.002 236.319 387.199C243.389 380.128 249.904 370.803 255.686 359.487L256.535 357.803L254.808 357.071C246.01 353.338 237.007 348.903 228.047 343.867L226.466 342.974L225.632 344.599C216.087 363.205 206.631 369.837 200.951 369.837C195.285 369.837 185.843 363.205 176.298 344.584C174.512 341.144 172.726 337.279 171.014 333.078C177.235 329.477 183.413 325.627 189.4 321.63L191.479 320.239L189.459 318.761C180.924 312.495 172.521 305.849 164.499 299.027L163.533 298.208L162.45 298.881C121.08 324.338 90.9818 329.199 82.6229 320.854C74.2494 312.481 79.1096 282.368 104.552 240.998C106.792 237.368 109.105 233.781 111.433 230.312C113.994 233.357 116.717 236.504 119.718 239.871C126.423 247.469 133.582 255.066 140.974 262.474C148.367 269.866 155.965 277.01 163.562 283.73C167.647 287.389 171.365 290.581 174.922 293.508C182.959 300.242 191.377 306.786 199.926 312.964C204.23 316.053 208.343 318.907 212.486 321.659C218.898 325.949 225.53 330.048 232.205 333.868C241.676 339.241 250.826 343.779 259.434 347.38C296.719 363.015 325.646 362.063 343.067 344.628C360.238 327.457 361.424 299.115 346.492 262.62C350.445 260.951 354.295 259.151 357.94 257.248C369.271 251.48 378.61 244.965 385.696 237.88C396.47 227.106 401.945 215.219 401.945 202.541C401.989 181.461 386.779 162.533 357.999 147.762ZM259.873 202.497C259.873 234.967 233.464 261.376 200.994 261.376C168.525 261.376 142.116 234.967 142.116 202.497C142.116 170.028 168.525 143.619 200.994 143.619C233.464 143.619 259.873 170.043 259.873 202.497Z"
                                      fill="#271744"></path>
                            </svg>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>