<?php

function getAvifyCustomOptions($product)
{
    $avifyCustomOptions = [];
    foreach ($product->get_meta_data() as $meta_datum) {
        if ($meta_datum->key == 'avify_custom_options') {
            $avifyCustomOptions = json_decode($meta_datum->value, true);
        }
    }
    return $avifyCustomOptions;
}

// Render custom options
add_action('woocommerce_before_add_to_cart_button', 'add_fields_before_add_to_cart');
function add_fields_before_add_to_cart()
{
    global $product;
    $avifyCustomOptions = getAvifyCustomOptions($product);

    echo "<div class='avify-custom-options'>";
    echo "<style>
            .avify-custom-options input:not([type='checkbox']):not([type='radio']) {width: 100%; padding: 5px}
            .avify-custom-options input[type='checkbox'] + label {margin-right: 10px}
            .avify-custom-options input[type='radio'] + label {margin-right: 10px}
            .avify-custom-options textarea {width: 100%; padding: 5px}
            .avify-custom-options .aco-field {border-bottom: rgba(0,0,0,0.2) solid 1px; margin-bottom: 15px; padding-bottom: 15px}
            .avify-custom-options p {margin: auto}
            .avify-custom-options .aco-spec {font-size: 12px; font-style: italic}
          </style>";
    foreach ($avifyCustomOptions as $avfCustomOpt) {
        if ($price = floatval($avfCustomOpt['price'])) {
            $price = wc_price($price, array('currency' => get_woocommerce_currency()));
        }
        $name = "avify_option_{$avfCustomOpt['option_id']}";
        echo "<div class='aco-field'>";
        echo "<p><label for='$name'><strong>{$avfCustomOpt['store_title']}" . ($price ? " (+{$price}) " : "") . ($avfCustomOpt['is_require'] ? '<span style="color: red">*</span>' : '') . " :</strong></label></p>";
        switch ($avfCustomOpt['type']) {
            case "field":
                echo "<p><input id='$name' type='text' name='$name' maxlength='{$avfCustomOpt['max_characters']}'/><span class='aco-spec'>" . __('Max Characters', 'avify-wordpress') . ": {$avfCustomOpt['max_characters']}</span></p>";
                break;
            case "area":
                echo "<p><textarea id='$name' name='$name' maxlength='{$avfCustomOpt['max_characters']}'></textarea><span class='aco-spec'>" . __('Max Characters', 'avify-wordpress') . ": {$avfCustomOpt['max_characters']}</span></p>";
                break;
            case "file":
                echo "<p><input id='$name' accept='{$avfCustomOpt['file_extension']}' type='file' name='$name'/><span class='aco-spec'>" . __('Allowed Extensions', 'avify-wordpress') . ": {$avfCustomOpt['file_extension']}</span></p>";
                if (floatval($avfCustomOpt['image_size_x'])) {
                    echo "<p><span class='aco-spec'>" . __('Max Width', 'avify-wordpress') . ": {$avfCustomOpt['image_size_x']}px</span></p>";
                }
                if (floatval($avfCustomOpt['image_size_y'])) {
                    echo "<p><span class='aco-spec'>" . __('Max Height', 'avify-wordpress') . ": {$avfCustomOpt['image_size_y']}px</span></p>";
                }
                break;
            case "drop_down":
                $options = '';
                foreach ($avfCustomOpt['values'] as $val) {
                    if ($price = floatval($val['price'])) {
                        $price = wc_price($price);
                    }
                    $options .= "<option value='{$val['option_type_id']}'>{$val['store_title']}" . ($price ? " (+{$price}) " : "") . "</option>";
                }
                echo "<p><select id='$name' name='$name'/>{$options}</select></p>";
                break;
            case "radio":
            case "checkbox":
                $radioCheckboxName = $avfCustomOpt['option_id'];
                $options = '';
                foreach ($avfCustomOpt['values'] as $val) {
                    if ($price = floatval($val['price'])) {
                        $price = wc_price($price);
                    }
                    if ($avfCustomOpt['type'] === "checkbox") {
                        $radioCheckboxName = $avfCustomOpt['option_id'] . "-" . $val['option_type_id'];
                    }

                    $name = "avify_option_{$radioCheckboxName}";
                    $options .= "<input id='$name' name='$name' type='{$avfCustomOpt['type']}' value='{$val['option_type_id']}'/>
                                 <label for='$name'>{$val['store_title']}" . ($price ? " (+{$price}) " : "") . "</label>";
                }
                echo "<p>$options</p>";
                break;
        }
        echo "</div>";
    }
    echo "</div>";
}

// Set custom data to cart item.
add_filter('woocommerce_add_cart_item_data', 'add_cart_item_data', 25, 2);
function add_cart_item_data($cart_item_data, $product_id)
{
    $product = wc_get_product($product_id);
    $avifyCustomOptions = getAvifyCustomOptions($product);

    //inputs
    foreach ($_POST as $postKey => $post) {
        if (strpos($postKey, 'avify_option') !== false) {
            $cart_item_data['avify_custom_options'][$postKey] = $post;
        }
    }

    //files
    if (!empty($_FILES)) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        foreach ($_FILES as $fileKey => $file) {
            if (strpos($fileKey, 'avify_option') !== false) {
                if (!empty($file['tmp_name'])) {
                    $id = str_replace('avify_option_', '', $fileKey);
                    $id = explode('-', $id);
                    $found = false;
                    $width = 0;
                    $height = 0;
                    foreach ($avifyCustomOptions as $avfCustomOpt) {
                        if ($avfCustomOpt['option_id'] == $id[0]) {
                            $found = true;
                            $width = floatval($avfCustomOpt['image_size_x']);
                            $height = floatval($avfCustomOpt['image_size_y']);
                            $allowedTypes = explode(',', $avfCustomOpt['file_extension']);

                            $type = $file['type'];
                            $type = str_replace('image/', '', $type);
                            foreach ($allowedTypes as &$allowedType) {
                                $allowedType = trim($allowedType);
                            }
                            if (!in_array($type, $allowedTypes)) {
                                throw new \Exception(__('Invalid file extension.', 'avify-wordpress'));
                            }
                        }
                    }

                    if ($found) {
                        $attachment_id = media_handle_upload($fileKey, 0);
                        if (is_wp_error($attachment_id)) {
                            throw new \Exception(__('Error uploading file.', 'avify-wordpress'));
                        }
                        $attachment = wp_prepare_attachment_for_js($attachment_id);
                        if ($width) {
                            if (floatval($attachment['width']) > $width) {
                                throw new \Exception(__('Invalid file width.', 'avify-wordpress'));
                            }
                        }
                        if ($height) {
                            if (floatval($attachment['height']) > $height) {
                                throw new \Exception(__('Invalid file height.', 'avify-wordpress'));
                            }
                        }
                        $cart_item_data['avify_custom_options'][$fileKey] = $attachment['url'];
                    }
                }
            }
        }
    }

    //validate empty fields
    foreach ($avifyCustomOptions as $avfCustomOpt) {
        $found = false;
        $isRequire = $avfCustomOpt['is_require'];
        $valueFound = '';
        foreach ($cart_item_data['avify_custom_options'] as $id => $value) {
            $id = str_replace('avify_option_', '', $id);
            $id = explode('-', $id);
            if ($avfCustomOpt['option_id'] == $id[0]) {
                $found = true;
                $valueFound = $value;
            }
        }

        if ($isRequire) {
            if (!$found || empty($valueFound)) {
                throw new \Exception(sprintf(__("Missing required field '%s'.", 'avify-wordpress'), $avfCustomOpt['store_title']));
            }
        }
    }

    return $cart_item_data;
}

// Display custom data on cart and checkout page.
add_filter('woocommerce_get_item_data', 'get_item_data', 25, 2);
function get_item_data($cart_data, $cart_item)
{
    if (isset($cart_item['avify_custom_options'])) {
        if (!empty($cart_item['avify_custom_options'])) {
            $product = wc_get_product($cart_item['product_id']);
            $avifyCustomOptions = getAvifyCustomOptions($product);
            foreach ($cart_item['avify_custom_options'] as $id => $value) {
                if (!empty($value)) {
                    $id = str_replace('avify_option_', '', $id);
                    $id = explode('-', $id);
                    foreach ($avifyCustomOptions as $avfCustomOpt) {
                        if ($avfCustomOpt['option_id'] == $id[0]) {
                            if ($price = floatval($avfCustomOpt['price'])) {
                                $price = wc_price($price, array('currency' => get_woocommerce_currency()));
                            }
                            switch ($avfCustomOpt['type']) {
                                case "file":
                                    $cart_data[] = array(
                                        'name' => $avfCustomOpt['store_title'] . ($price ? " (+{$price}) " : ""),
                                        'display' => "<img width='50' alt='{$avfCustomOpt['store_title']}' src='$value'/>"
                                    );
                                    break;
                                case "field":
                                case "area":
                                    $cart_data[] = array(
                                        'name' => $avfCustomOpt['store_title'] . ($price ? " (+{$price}) " : ""),
                                        'display' => $value
                                    );
                                    break;
                                case "drop_down":
                                case "radio":
                                case "checkbox":
                                    foreach ($avfCustomOpt['values'] as $val) {
                                        if ($val['option_type_id'] == $value) {
                                            if ($price = floatval($val['price'])) {
                                                $price = wc_price($price, array('currency' => get_woocommerce_currency()));
                                            }
                                            $cart_data[] = array(
                                                'name' => $avfCustomOpt['store_title'] . ($price ? " (+{$price}) " : ""),
                                                'display' => $val['store_title']
                                            );
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
    return $cart_data;
}

// Add custom options price to current price
add_action('woocommerce_before_calculate_totals', 'add_custom_price');
function add_custom_price(WC_Cart $cart_object)
{
    foreach ($cart_object->get_cart() as $item) {
        /** @var $itemData WC_Product * */
        $itemData = $item['data'];
        $product = wc_get_product($itemData->get_id());
        $avifyCustomOptions = getAvifyCustomOptions($product);
        if (isset($item['avify_custom_options'])) {
            $plus = 0;
            foreach ($avifyCustomOptions as $avfCustomOpt) {
                foreach ($item['avify_custom_options'] as $id => $value) {
                    $id = str_replace('avify_option_', '', $id);
                    $id = explode('-', $id);
                    if ($avfCustomOpt['option_id'] == $id[0]) {
                        switch ($avfCustomOpt['type']) {
                            case "field":
                            case "area":
                            case "file":
                                $plus += floatval($avfCustomOpt['price']);
                                break;
                            case "drop_down":
                            case "radio":
                            case "checkbox":
                                foreach ($avfCustomOpt['values'] as $val) {
                                    if ($val['option_type_id'] == $value) {
                                        $plus += floatval($val['price']);
                                    }
                                }
                                break;
                        }
                    }
                }
            }
            $itemData->set_price(floatval($itemData->get_price()) + $plus);
        }
    }
}

// Save custom data on order line item.
add_action('woocommerce_checkout_create_order_line_item', 'add_order_item_data', 10, 3);
function add_order_item_data(WC_Order_Item_Product $cartItem, string $cartItemKey, array $values): void
{
    if (isset($values['avify_custom_options'])) {
        if (!empty($values['avify_custom_options']) && is_array($values['avify_custom_options'])) {
            $cartItem->add_meta_data('avify_custom_options', json_encode($values['avify_custom_options']), true);
        }
    }
}

// Display custom data post order
add_filter('woocommerce_hidden_order_itemmeta', 'hide_order_item_meta_fields');
function hide_order_item_meta_fields($fields)
{
    $fields[] = 'avify_custom_options';
    return $fields;
}

add_action('woocommerce_before_order_itemmeta', 'get_order_item_data', 10, 3);
add_action('woocommerce_order_item_meta_start', 'get_order_item_data', 10, 3);
/**
 * @param $item_id
 * @param $item WC_Order_Item_Product
 */
function get_order_item_data($item_id, $item)
{
    if (isset($item['avify_custom_options'])) {
        if (!empty($item['avify_custom_options'])) {
            if (is_string($item['avify_custom_options'])) {
                $item['avify_custom_options'] = json_decode($item['avify_custom_options'], true);
            }
            $product = wc_get_product($item['product_id']);
            $avifyCustomOptions = getAvifyCustomOptions($product);
            foreach ($item['avify_custom_options'] as $id => $value) {
                if (!empty($value)) {
                    $id = str_replace('avify_option_', '', $id);
                    $id = explode('-', $id);
                    foreach ($avifyCustomOptions as $avfCustomOpt) {
                        if ($price = floatval($avfCustomOpt['price'])) {
                            $price = wc_price($price, array('currency' => get_woocommerce_currency()));
                        }
                        if ($avfCustomOpt['option_id'] == $id[0]) {
                            echo "<p><strong>{$avfCustomOpt['store_title']}" . ($price ? " (+{$price}) " : "") . ":</strong></p>";
                            switch ($avfCustomOpt['type']) {
                                case "file":
                                    echo "<img width='50' alt='{$avfCustomOpt['store_title']}' src='$value'/>";
                                    break;
                                case "field":
                                case "area":
                                    echo $value;
                                    break;
                                case "drop_down":
                                case "radio":
                                case "checkbox":
                                    foreach ($avfCustomOpt['values'] as $val) {
                                        if ($val['option_type_id'] == $value) {
                                            if ($price = floatval($val['price'])) {
                                                $price = wc_price($price, array('currency' => get_woocommerce_currency()));
                                            }
                                            echo $val['store_title'] . ($price ? " (+{$price}) " : "");
                                        }
                                    }
                                    break;
                            }
                        }
                    }
                }
            }
        }
    }
}
