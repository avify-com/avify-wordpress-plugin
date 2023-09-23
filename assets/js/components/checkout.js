export default class Checkout {
    constructor() {
        this.init();
    }

    cleanHTML() {
        $('body p').each(function () {
            if ($(this).text().trim() === '') {
                $(this).remove();
            }
        });
        $('body br').each(function () {
            $(this).remove();
        });
    }

    init() {
        const self = this;

        self.body = $("body");
        self.currentStep = 'personal';
        self.cleanHTML();

        // avify custom fields
        self.avfBillingName = $("#avf_billing_name");
        self.avfBillingLastName = $("#avf_billing_lastname");
        self.avfBillingEmail = $("#avf_billing_email");
        self.avfBillingPhone = $("#avf_billing_tel");
        self.avfBillingCountry = $("#avf_billing_country");
        self.avfBillingState = $("#avf_billing_state");
        self.avfBillingCity = $("#avf_billing_city");
        self.avfBillingDistrict = $("#avf_billing_district");
        self.avfBillingZip = $("#avf_billing_postal");
        self.avfBillingAddress = $("#avf_billing_address");
        //--
        self.differentBillingAddress = false;
        self.wooShipToDifSelector = '#ship-to-different-address-checkbox';
        self.avfDifBillingContainer = $(".step-content-avf-dif-billing-form");
        self.avfDifBillingCheckbox = $(".step-content-avf-dif-billing-checkbox");
        self.avfDifBillingName = $("#avf_dif_billing_name");
        self.avfDifBillingLastName = $("#avf_dif_billing_lastname");
        self.avfDifBillingCountry = $("#avf_dif_billing_country");
        self.avfDifBillingState = $("#avf_dif_billing_state");
        self.avfDifBillingCity = $("#avf_dif_billing_city");
        self.avfDifBillingDistrict = $("#avf_dif_billing_district");
        self.avfDifBillingZip = $("#avf_dif_billing_postal");
        self.avfDifBillingAddress = $("#avf_dif_billing_address");
        //--
        self.avfSecondStep = $("#avf_to_second_step_button");
        self.avfThirdStep = $("#avf_to_third_step_button");
        self.avfShipOrPick = $("#avf_shipping_or_pick");
        self.avfIsPickUp = false;
        self.fileUploader = $("#alg_checkout_files_upload_form_1");
        //--
        self.avfShippingMap = $("#avf_map");
        self.avfShippingMapClicked = false;
        self.avfShippingMethods = $("#avf_shipping_methods");
        self.avfShippingLoader = $("#avf_shipping_methods_loader");
        self.avfShippingMethodsHTML = $("#avf_shipping_methods .step-content-shipping-var-item").addClass("avf-new-method").parent().html()
        //--
        self.avfPlaceOrder = $("#avf_checkout_button");
        self.avfCheckoutLoader = $(".avf-checkout-loader");

        // woocommerce core fields
        self.wooShippingName = $("#shipping_first_name");
        self.wooShippingLastName = $("#shipping_last_name");
        self.wooShippingCountry = $("#shipping_country");
        self.wooShippingState = $("#shipping_state");
        self.wooShippingCity = $("#shipping_city");
        self.wooShippingZip = $("#shipping_postcode");
        self.wooShippingAddress1 = $("#shipping_address_1");
        self.wooShippingAddress2 = $("#shipping_address_2");
        //--
        self.wooBillingName = $("#billing_first_name");
        self.wooBillingLastName = $("#billing_last_name");
        self.wooBillingEmail = $("#billing_email");
        self.wooBillingPhone = $("#billing_phone");
        self.wooBillingCountry = $("#billing_country");
        self.wooBillingState = $("#billing_state");
        self.wooBillingCity = $("#billing_city");
        self.wooBillingZip = $("#billing_postcode");
        self.wooBillingAddress1 = $("#billing_address_1");
        self.wooBillingAddress2 = $("#billing_address_2");

        if (
            $("body.woocommerce-checkout").length &&
            $(".type-avify-checkout").length &&
            !$(".woocommerce-order-received").length
        ) {
            $(".yv-header-nav, .yv-header-search, .yv-header-cart").hide();
            self.initObserve();
            self.initStep1();
            self.initStep2();
            self.initStep3();
            self.executeObserve();
        } else {
            $("section.type-avify-checkout .step-item").addClass("active completed");
            let buttonInfo = $(".woocommerce-info a.button"),
                avfRegisterButton = $("#avf_register_after_checkout")
            if (!buttonInfo) {
                avfRegisterButton.hide();
            } else {
                avfRegisterButton.attr("href", buttonInfo.attr("href"));
            }
            $("#avf_order_number").text("#" + $(".woocommerce-order-overview__order strong").text());
        }

        self.body.css('opacity', '1');
    }

    // --

    areInputsNotEmpty(inputs) {
        let success = true;
        inputs.map(input => {
            const valid = (input.val() !== "" && input.val() !== '');
            if (!valid) {
                console.log('required input empty:');
                console.log(input);
                success = false;
            }
        });
        return success;
    }

    completeAvfSelectorWithWooSelector(wooOptions, avfSelector, wooSelector) {
        const self = this;
        avfSelector.html("");
        wooOptions.each(function () {
            avfSelector.append($(this).clone());
        });
        if (self.areInputsNotEmpty([wooSelector])) {
            avfSelector.val(wooSelector.val());
        } else {
            avfSelector.find("option").eq(0).attr("selected", "selected");
        }
    }

    fireKeydownEventOnElement($elem) {
        $elem.trigger("keydown");
    }

    // --

    checkIfCanOpenSecondStep() {
        const self = this;
        if (
            self.avfBillingName.val() &&
            self.avfBillingLastName.val() &&
            self.avfBillingEmail.val() &&
            self.avfBillingPhone.val()) {
            self.avfSecondStep.removeClass("var-disabled");
        } else {
            self.avfSecondStep.addClass("var-disabled");
        }
    }

    checkIfCanOpenThirdStep() {
        const self = this;
        self.avfThirdStep.addClass("var-disabled");
        const selectedMethod = $('#shipping_method li input:checked');
        const allMethods = $('#shipping_method li input');
        if (selectedMethod.length || allMethods.length === 1) {
            const method = selectedMethod.length ? selectedMethod : allMethods;
            if (self.avfIsPickUp) {
                if (method.val().startsWith('avfdeliveries-instorepickup')) {
                    self.avfThirdStep.removeClass("var-disabled");
                }
            } else {
                if (self.areInputsNotEmpty([
                    self.avfBillingCountry, self.avfBillingState, self.avfBillingCity,
                    self.avfBillingDistrict, self.avfBillingZip, self.avfBillingAddress
                ])) {
                    if (!method.val().startsWith('avfdeliveries-instorepickup')) {
                        self.avfThirdStep.removeClass("var-disabled");
                    }
                }
            }
        }
    }

    // --

    shippingZonesLabels() {
        const self = this;
        const na = (a, b = true) => {
            if (a.is(".avf_form-row-1-of-2")) {
                if (b) {
                    a.removeClass("avf_form-row-1-of-2");
                    self.avfBillingDistrict.parents(".avf_form-input").hide();
                    self.avfBillingDistrict.val("N/A");
                    self.avfBillingDistrict.trigger("input");
                }
            } else {
                if (!b) {
                    a.addClass("avf_form-row-1-of-2");
                    self.avfBillingDistrict.parents(".avf_form-input").show();
                    self.avfBillingDistrict.val("");
                }
            }
        }
        const complete = (a, c) => {
            a.parents(".avf_form-input")
                .find(".avf_form-input-label b")
                .text(a.parents(".avf_form-input").find(".avf_form-input-label").data(`${c}text`));
        }

        if (['CR', 'Costa Rica'].includes(self.avfBillingCountry.val())) {
            na($("#avf_district_and_postal_row"));
            na($("#avf_dif_district_and_postal_row"));
            complete(self.avfBillingCity, 'cr-');
            complete(self.avfDifBillingCity, 'cr-');
        } else {
            na($("#avf_district_and_postal_row"), false);
            na($("#avf_dif_district_and_postal_row"), false);

            if (['MX', 'México', 'Mexico'].includes(self.avfBillingCountry.val())) {
                complete(self.avfBillingState, 'mx-');
                complete(self.avfBillingCity, 'mx-');
                complete(self.avfBillingDistrict, 'mx-');
                complete(self.avfDifBillingState, 'mx-');
                complete(self.avfDifBillingCity, 'mx-');
                complete(self.avfDifBillingDistrict, 'mx-');
            } else {
                complete(self.avfBillingState, '');
                complete(self.avfBillingCity, '');
                complete(self.avfBillingDistrict, '');
                complete(self.avfDifBillingState, '');
                complete(self.avfDifBillingCity, '');
                complete(self.avfDifBillingDistrict, '');
            }
        }
    }

    initAvfBillingForm() {
        const self = this;
        if (self.wooBillingCountry.attr("type") === "hidden") {
            self.avfDifBillingCountry.val(
                $("#billing_country_field")
                    .find(".woocommerce-input-wrapper strong")
                    .text()
            );
        } else {
            self.avfDifBillingCountry.replaceWith('<select id="' + self.avfDifBillingCountry.attr("id") + '">');
            self.avfDifBillingCountry = $("#avf_dif_billing_country");
            self.bindDifCountryChange();
            self.completeAvfSelectorWithWooSelector(
                $("#billing_country option"),
                self.avfDifBillingCountry,
                self.wooBillingCountry
            );
        }
        self.switchDifCityHTMLType();
        self.completeAvfSelectorWithWooSelector(
            $("#billing_state option"),
            self.avfDifBillingState,
            self.wooBillingState
        );
        self.completeAvfSelectorWithWooSelector(
            $("#billing_city option"),
            self.avfDifBillingCity,
            self.wooBillingCity
        );
    }

    bindDifCountryChange() {
        const self = this;
        self.avfDifBillingCountry.bind("input propertychange", function () {
            self.wooBillingCountry.val(self.avfDifBillingCountry.val());
            self.wooBillingCountry.trigger("change");
            self.completeAvfSelectorWithWooSelector(
                $("#billing_state option"),
                self.avfDifBillingState,
                self.avfBillingState
            );
            self.switchDifCityHTMLType();
            self.shippingZonesLabels();
            self.updateBillingAddressSummary();
        });
    }

    bindCountryChange() {
        const self = this;
        self.avfBillingCountry.bind("input propertychange", function () {
            self.wooShippingCountry.val(self.avfBillingCountry.val());
            self.wooShippingCountry.trigger("change");
            if (!self.differentBillingAddress) {
                self.wooBillingCountry.val(self.avfBillingCountry.val());
                self.wooBillingCountry.trigger("change");
            }
            self.completeAvfSelectorWithWooSelector(
                $("#shipping_state option"),
                self.avfBillingState,
                self.wooShippingState
            );
            self.switchCityHTMLType();
            self.shippingZonesLabels();
            self.checkIfCanOpenThirdStep();
        });
    }

    // --

    bindCityChange() {
        const self = this;
        self.avfBillingCity.bind("input propertychange", function () {
            self.wooShippingCity.val(self.avfBillingCity.val());
            self.wooShippingCity.trigger("change");
            if (!self.differentBillingAddress) {
                self.wooBillingCity.val(self.avfBillingCity.val());
                self.wooBillingCity.trigger("change");
            }
            self.avfBillingZip.val(self.wooShippingZip.val());
            self.checkIfCanOpenThirdStep();
        });
    }

    bindDifCityChange() {
        const self = this;
        self.avfDifBillingCity.bind("input propertychange", function () {
            self.wooBillingCity.val(self.avfDifBillingCity.val());
            self.wooBillingCity.trigger("change");
            self.avfDifBillingZip.val(self.wooBillingZip.val());
            self.updateBillingAddressSummary();
        });
    }

    switchCityHTMLType() {
        const self = this,
            id = self.avfBillingCity.attr("id");
        if (self.wooShippingCity.parent().find("select")[0]) {
            self.avfBillingCity.replaceWith('<select id="' + id + '">');
        } else {
            self.avfBillingCity.replaceWith('<input id="' + id + '">');
        }
        self.avfBillingCity = $("#avf_billing_city");
        self.completeAvfSelectorWithWooSelector(
            $("#shipping_city option"),
            self.avfBillingCity,
            self.wooShippingCity
        );
        self.bindCityChange();
        self.avfBillingCity.trigger("propertychange");
    }

    switchDifCityHTMLType() {
        const self = this,
            id = self.avfDifBillingCity.attr("id");
        if (self.wooBillingCity.parent().find("select")[0]) {
            self.avfDifBillingCity.replaceWith('<select id="' + id + '">');
        } else {
            self.avfDifBillingCity.replaceWith('<input id="' + id + '">');
        }

        self.avfDifBillingCity = $("#avf_dif_billing_city");
        self.completeAvfSelectorWithWooSelector(
            $("#billing_city option"),
            self.avfDifBillingCity,
            self.wooBillingCity
        );
        self.bindDifCityChange();
        self.avfDifBillingCity.trigger("propertychange");
    }

    updateBillingAddressSummary() {
        const self = this,
            summary = $("#avf_billing_summary");
        if (self.differentBillingAddress) {
            summary.html(`
				${self.avfDifBillingCountry.val()} |
				${self.avfDifBillingState.val()} |
				${self.avfDifBillingCity.val()} |
				${self.avfDifBillingDistrict.val()} |
				${self.avfDifBillingAddress.val()} |
				${self.avfDifBillingZip.val()}
			`);
        } else {
            if (avfIsVirtual) {
                summary.html(`
				${self.wooBillingCountry.val()} |
				${self.wooBillingState.val()} |
				${self.wooBillingCity.val()} |
				${self.wooBillingAddress2.val()} |
				${self.wooBillingAddress1.val()} |
				${self.wooBillingZip.val()}
			`);
            } else {
                summary.html(`
				${self.avfBillingCountry.val()} |
				${self.avfBillingState.val()} |
				${self.avfBillingCity.val()} |
				${self.avfBillingDistrict.val()} |
				${self.avfBillingAddress.val()} |
				${self.avfBillingZip.val()}
			`);
            }
        }
    }

    // --

    initStep1() {
        const self = this,
            stepContent = $(".step-content"),
            stepTab = $("section.type-avify-checkout .step-item");

        // open first step
        stepContent.eq(0).addClass("active");
        stepTab.eq(0).addClass("active");

        // steps tabs events
        stepTab.on("click", function () {
            if ($(this).hasClass("active")) {
                stepContent.removeClass("active");
                stepContent.eq($(this).index()).addClass("active");
                $(this).nextAll(".step-item").removeClass("active");
                $(this).nextAll(".step-item").removeClass("completed");
                $(this).removeClass("completed active");
                $(this).addClass("active");
                self.currentStep = stepContent.attr('data-step');
                self.executeObserve();
            }
        });

        // initial values
        const shipToDif = $(`${self.wooShipToDifSelector}:checked`).length;
        self.avfBillingName.val(shipToDif ? self.wooShippingName.val() : self.wooBillingName.val());
        self.avfBillingLastName.val(shipToDif ? self.wooShippingLastName.val() : self.wooBillingLastName.val());
        self.avfBillingEmail.val(shipToDif ? self.wooBillingEmail.val() : self.wooBillingEmail.val());
        self.avfBillingPhone.val(shipToDif ? self.wooBillingPhone.val() : self.wooBillingPhone.val());

        // binds
        self.avfBillingName.bind("input propertychange", function () {
            self.wooBillingName.val(self.avfBillingName.val());
            self.wooShippingName.val(self.avfBillingName.val());
            self.checkIfCanOpenSecondStep();
        });
        self.avfBillingLastName.bind("input propertychange", function () {
            self.wooBillingLastName.val(self.avfBillingLastName.val());
            self.wooShippingLastName.val(self.avfBillingLastName.val());
            self.checkIfCanOpenSecondStep();
        });
        self.avfBillingEmail.bind("input propertychange", function () {
            self.wooBillingEmail.val(self.avfBillingEmail.val());
            self.checkIfCanOpenSecondStep();
        });
        self.avfBillingPhone.bind("input propertychange", function () {
            self.wooBillingPhone.val(self.avfBillingPhone.val());
            self.checkIfCanOpenSecondStep();
        });

        // clicks
        self.avfSecondStep.on("click", function () {
            stepContent.removeClass("active");
            stepContent.eq(1).addClass("active");
            stepTab.eq(0).addClass("completed");
            stepTab.eq(1).addClass("active");
            self.currentStep = stepContent.eq(1).attr('data-step');
            self.executeObserve();
        });
        self.checkIfCanOpenSecondStep();
    }

    initStep2() {
        const self = this,
            stepContent = $(".step-content"),
            stepTab = $("section.type-avify-checkout .step-item");

        // initial values
        const shipToDif = $(`${self.wooShipToDifSelector}:checked`).length;
        if (self.wooShippingCountry.attr("type") === "hidden") {
            self.avfBillingCountry.val(
                $("#shipping_country_field").find(".woocommerce-input-wrapper strong").text()
            );
        } else {
            self.avfBillingCountry.replaceWith('<select id="' + self.avfBillingCountry.attr("id") + '">');
            self.avfBillingCountry = $("#avf_billing_country");
            self.completeAvfSelectorWithWooSelector(
                $("#shipping_country option"),
                self.avfBillingCountry,
                shipToDif ? self.wooShippingCountry : self.wooBillingCountry
            );
            self.bindCountryChange();
        }
        self.completeAvfSelectorWithWooSelector(
            $("#shipping_state option"),
            self.avfBillingState,
            shipToDif ? self.wooShippingState : self.wooBillingState
        );
        self.switchCityHTMLType();
        self.completeAvfSelectorWithWooSelector(
            $("#shipping_city option"),
            self.avfBillingCity,
            shipToDif ? self.wooShippingCity : self.wooBillingCity
        );
        self.avfBillingDistrict.val(shipToDif ? self.wooShippingAddress2.val() : self.wooBillingAddress2.val());
        self.avfBillingZip.val(shipToDif ? self.wooShippingZip.val() : self.wooBillingZip.val());
        self.avfBillingAddress.val(shipToDif ? self.wooShippingAddress1.val() : self.wooBillingAddress1.val());
        $('.step-content-shipping-method-content-item-2').hide();

        // map
        const avfShippingMapOrig = $("#lpac-map-container");
        if (avfShippingMapOrig.length) {
            const avfShippingMapPlace = $(".step-content-map-container");
            avfShippingMapPlace.append(avfShippingMapOrig);
        } else {
            $(".step-content-map").hide();
        }

        // binds
        self.bindCountryChange();
        self.avfBillingState.bind("input propertychange", function () {
            self.wooShippingState.val(self.avfBillingState.val());
            self.wooShippingState.trigger("change");
            if (!self.differentBillingAddress) {
                self.wooBillingState.val(self.avfBillingState.val());
                self.wooBillingState.trigger("change");
            }
            self.switchCityHTMLType();
            self.checkIfCanOpenThirdStep();
        });
        self.bindCityChange();
        self.avfBillingDistrict.bind("input propertychange", function () {
            self.wooShippingAddress2.val(self.avfBillingDistrict.val());
            self.fireKeydownEventOnElement(self.wooShippingAddress1);
            if (!self.differentBillingAddress) {
                self.wooBillingAddress2.val(self.avfBillingDistrict.val());
                self.fireKeydownEventOnElement(self.wooBillingAddress2);
            }
            self.checkIfCanOpenThirdStep();
        });
        self.avfBillingZip.bind("input propertychange", function () {
            self.wooShippingZip.val(self.avfBillingZip.val());
            self.fireKeydownEventOnElement(self.wooShippingZip);
            if (!self.differentBillingAddress) {
                self.wooBillingZip.val(self.avfBillingZip.val());
                self.fireKeydownEventOnElement(self.wooBillingZip);
            }
            self.avfShippingMethods.hide();
            self.avfShippingLoader.show();
            self.checkIfCanOpenThirdStep();
        });
        self.avfBillingAddress.bind("input propertychange", function () {
            self.wooShippingAddress1.val(self.avfBillingAddress.val());
            self.fireKeydownEventOnElement(self.wooShippingAddress1);
            if (!self.differentBillingAddress) {
                self.wooBillingAddress1.val(self.avfBillingAddress.val());
                self.fireKeydownEventOnElement(self.wooBillingAddress1);
            }
            self.checkIfCanOpenThirdStep();
        });

        // clicks
        self.avfShippingMap.on("click", function () {
            self.avfShippingMapClicked = true;
        });
        self.avfShipOrPick.on("click", function () {
            self.avfThirdStep.addClass("var-disabled");
            if ($("#avf_shipping_or_pick_1").is(":checked")) {
                $(".step-content-shipping-method-content-item-1").show();
                $(".step-content-shipping-method-content-item-2").hide();
                self.avfIsPickUp = false;
            } else {
                $(".step-content-shipping-method-content-item-2").show();
                $(".step-content-shipping-method-content-item-1").hide();
                self.avfIsPickUp = true;
            }
            self.checkIfCanOpenThirdStep();
        });
        self.avfThirdStep.on("click", function () {
            stepContent.removeClass("active");
            stepContent.eq(2).addClass("active");
            stepTab.eq(1).addClass("completed");
            stepTab.eq(2).addClass("active");
            self.currentStep = stepContent.eq(2).attr('data-step')
            self.executeObserve();
        });
        self.avfShippingLoader.hide();
    }

    initStep3() {
        const self = this;

        self.avfDifBillingName.bind("input propertychange", function () {
            self.wooBillingName.val(self.avfDifBillingName.val());
        });
        self.avfDifBillingLastName.bind("input propertychange", function () {
            self.wooBillingLastName.val(self.avfDifBillingLastName.val());
        });
        self.bindDifCountryChange();
        self.avfDifBillingState.bind("input propertychange", function () {
            self.wooBillingState.val(self.avfDifBillingState.val());
            self.wooBillingState.trigger("change");
            self.switchDifCityHTMLType();
            self.updateBillingAddressSummary();
        });
        self.bindDifCityChange();
        self.avfDifBillingDistrict.bind("input propertychange", function () {
            self.wooBillingAddress2.val(self.avfDifBillingDistrict.val());
            self.fireKeydownEventOnElement(self.wooBillingAddress2);
            self.updateBillingAddressSummary();
        });
        self.avfDifBillingZip.bind("input propertychange", function () {
            self.wooBillingZip.val(self.avfDifBillingZip.val());
            self.fireKeydownEventOnElement(self.wooBillingZip);
            self.updateBillingAddressSummary();
        });
        self.avfDifBillingAddress.bind("input propertychange", function () {
            self.wooBillingAddress1.val(self.avfDifBillingAddress.val());
            self.fireKeydownEventOnElement(self.wooBillingAddress1);
            self.updateBillingAddressSummary();
        });
        self.updateBillingAddressSummary();

        // different billing address
        self.avfDifBillingContainer.hide();
        self.avfDifBillingCheckbox.on("click", function () {
            if (!$(this).hasClass('checked')) {
                $(this).addClass('checked');
                self.avfDifBillingContainer.show();
                self.differentBillingAddress = true;
                self.initAvfBillingForm();
                if (!$(`${self.wooShipToDifSelector}:checked`).length) {
                    $(`${self.wooShipToDifSelector}`).click();
                }
            } else {
                $(this).removeClass('checked');
                self.avfDifBillingContainer.hide();
                self.differentBillingAddress = false;
                if ($(`${self.wooShipToDifSelector}:checked`).length) {
                    $(`${self.wooShipToDifSelector}`).click();
                }
            }
            self.updateBillingAddressSummary();
        });
        if ($(`${self.wooShipToDifSelector}:checked`).length) {
            $(`${self.wooShipToDifSelector}`).click(); // uncheck on load
        }

        if (avfShowElectronicInvoice) {
            $(".step-content-avf-electronic-invoice-holder").hide();
            $(".step-content-avf-electronic-invoice-label").on("click", function () {
                if (!$(this).hasClass('checked')) {
                    $(this).addClass('checked');
                    $(".step-content-avf-electronic-invoice-holder").show();
                    $("#additional_want_electronic_invoice").prop("checked", true);
                } else {
                    $(this).removeClass('checked');
                    $(".step-content-avf-electronic-invoice-holder").hide();
                    $("#additional_want_electronic_invoice").prop("checked", false);
                }
            });
            $("#additional_identification_type option").each(function () {
                $("#avf_additional_identification_type").append($(this).clone());
            });
            $("#avf_additional_identification_type").bind("input propertychange", function () {
                $("#additional_identification_type").val($(this).val());
                $("#additional_identification_type").trigger("change");
            });
            $("#avf_additional_identification_number").bind("input propertychange", function () {
                $("#additional_identification_number").val($(this).val());
            });
        } else {
            $('.step-content-electronic-invoice').hide();
        }

        self.avfPlaceOrder.on("click", function () {
            if (self.avfIsPickUp) {
                // autocomplete
                setTimeout(function () {
                    if (self.wooBillingCountry.is("select") && !self.wooBillingCountry.val()) {
                        self.wooBillingCountry.val(self.wooBillingCountry.find("option").eq(1).val());
                        self.wooBillingCountry.trigger("change");
                    }
                    if (self.wooShippingCountry.is("select") && !self.wooShippingCountry.val()) {
                        self.wooShippingCountry.val(self.wooShippingCountry.find("option").eq(1).val());
                        self.wooShippingCountry.trigger("change");
                    }
                }, 100);
                setTimeout(function () {
                    if (self.avfBillingState.is("select") && !self.avfBillingState.val()) {
                        self.avfBillingState.val(self.avfBillingState.find("option").eq(1).val());
                        self.avfBillingState.trigger("propertychange");
                    }
                    if (self.wooShippingState.is("select") && !self.wooShippingState.val()) {
                        self.wooShippingState.val(self.wooShippingState.find("option").eq(1).val());
                        self.wooShippingState.trigger("change");
                    }
                }, 200);
                setTimeout(function () {
                    if (self.wooBillingCity.is("select") && !self.wooBillingCity.val()) {
                        self.wooBillingCity.val(self.wooBillingCity.find("option").eq(1).val());
                        self.wooBillingCity.trigger("change");
                    }
                    if (self.wooShippingCity.is("select") && !self.wooShippingCity.val()) {
                        self.wooShippingCity.val(self.wooShippingCity.find("option").eq(1).val());
                        self.wooShippingCity.trigger("change");
                    }
                }, 300);
                setTimeout(function () {
                    if (!self.wooBillingCountry.val())
                        self.wooBillingCountry.val("N/A");
                    if (!self.wooShippingCountry.val())
                        self.wooShippingCountry.val("N/A");

                    if (!self.avfBillingState.val())
                        self.avfBillingState.val("N/A");
                    if (!self.wooShippingState.val())
                        self.wooShippingState.val("N/A");

                    if (!self.wooBillingCity.val())
                        self.wooBillingCity.val("N/A");
                    if (!self.wooShippingCity.val())
                        self.wooShippingCity.val("N/A");

                    if (!self.wooBillingZip.val())
                        self.wooBillingZip.val("N/A");
                    if (!self.wooShippingZip.val())
                        self.wooShippingZip.val("N/A");

                    if (!self.wooBillingAddress1.val())
                        self.wooBillingAddress1.val("N/A");
                    if (!self.wooShippingAddress1.val())
                        self.wooShippingAddress1.val("N/A");
                }, 400);
            }

            self.avfCheckoutLoader.addClass("show");
            setTimeout(function () {
                $("#place_order").click();
            }, 500);
            setTimeout(function () {
                self.avfCheckoutLoader.removeClass("show");
            }, 5000);
        });
    }

    // --

    observeShippingMethods() {
        if (avfIsVirtual) {
            return;
        }

        const self = this,
            wooShippingMethods = self.body.find("#shipping_method li"),
            inStoreOption = $(".step-content-shipping-method-item"),
            inStoreContainer = document.querySelector('.step-content-self-pickup-info');


        self.avfShippingMethods.html("");
        inStoreContainer.innerHTML = "";
        if (wooShippingMethods.length) {
            let inStoreAvailable = false;
            wooShippingMethods.each(function () {
                self.avfShippingMethods.append(self.avfShippingMethodsHTML);
                const wooInput = $(this).find("input"),
                    avfNewMethod = self.avfShippingMethods.find(".avf-new-method");
                avfNewMethod.find("input").attr('value', wooInput.val());
                avfNewMethod.attr("data-shipping-value", wooInput.val());
                avfNewMethod.find(".step-content-shipping-var-item-text .avf_txt").text($(this).find("label")[0].childNodes[0].nodeValue);
                avfNewMethod.find(".step-content-shipping-var-item-text-2 .avf_txt").text($(this).find(".amount").text());
                if (wooInput.is(":checked")) {
                    avfNewMethod.find("input").prop("checked", true);
                }

                // in store methods
                if (wooInput.val().startsWith('avfdeliveries-instorepickup')) {
                    inStoreAvailable = true;
                    const instoreMethod = document.createElement('div');
                    instoreMethod.className = avfNewMethod.attr('class');
                    instoreMethod.innerHTML = avfNewMethod.html();
                    inStoreContainer.appendChild(instoreMethod);
                    instoreMethod.addEventListener('click', (e) => {
                        wooInput.click();
                        self.checkIfCanOpenThirdStep();
                    });
                    if (wooInput.is(":checked")) {
                        instoreMethod.querySelector("input").setAttribute("checked", "true");
                    }
                    avfNewMethod.remove();
                } else {
                    avfNewMethod.removeClass("avf-new-method");
                }
            });
            if (inStoreAvailable) {
                inStoreOption.show();
            } else {
                inStoreOption.hide();
            }

            // on click avify shipping methods
            if (!self.avfShippingMethods.hasClass('evented')) {
                self.avfShippingMethods.addClass('evented')
                self.avfShippingMethods.on("click", ".step-content-shipping-var-item", (e) => {
                    const shippingMethod = $(e.target).attr('value');
                    if (shippingMethod) {
                        self.body
                            .find("#shipping_method li")
                            .find(`input[value='${shippingMethod}']`)
                            .click();
                        self.checkIfCanOpenThirdStep();
                    }
                });
            }
        } else {
            // todo show message
        }

        self.avfShippingMethods.show();
        self.avfShippingLoader.hide();
        self.checkIfCanOpenThirdStep();
    }

    relocateBankAttachment() {
        const self = this;

        if ($("#payment .payment_method_cheque").length) {
            if (!$("#payment .payment_method_cheque .avf-banktransfer-attachment").length) {
                self.body.find(".payment_box.payment_method_cheque").append('<div class="avf-banktransfer-attachment"></div>');
            }
        } else {
            self.fileUploader.hide();
            return;
        }

        let from = $(".woocommerce-notices-wrapper"),
            to = $(".avf-banktransfer-attachment"),
            y = to.offset().top - from.offset().top,
            x = to.offset().left - from.offset().left;
        to.css("height", self.fileUploader.height());
        self.fileUploader.css("top", y + "px");
        self.fileUploader.css("left", x + "px");
        self.fileUploader.css("width", to.width());
        if (avfAttachmentRequired) {
            if ($(".alg_checkout_files_upload_result_1 img").length) {
                self.avfPlaceOrder.removeClass("var-disabled");
            } else {
                self.avfPlaceOrder.addClass("var-disabled");
            }
        }

        if (self.currentStep === 'payment') {
            const selectedMethod = $("input[name='payment_method']:checked");
            if (selectedMethod.length) {
                if (selectedMethod.attr('id') === 'payment_method_cheque') {
                    self.fileUploader.show();
                } else {
                    self.fileUploader.hide();
                    self.avfPlaceOrder.removeClass("var-disabled");
                }
            }
        } else {
            self.fileUploader.hide();
        }
    }

    relocatePayments() {
        const self = this;

        // relocate
        const wooPaymentMethods = self.body.find(".wc_payment_methods")
        if (self.currentStep === 'payment') {
            wooPaymentMethods.show();
            self.fileUploader.show();

            const wooPaymentContainer = $("#payment"),
                avfPaymentContainer = $(".step-content-payment-container-for-woo"),
                y = avfPaymentContainer.offset().top - wooPaymentContainer.offset().top,
                x = avfPaymentContainer.offset().left - wooPaymentContainer.offset().left;
            avfPaymentContainer.css("height", wooPaymentMethods.height());
            wooPaymentMethods.css("top", y + "px");
            wooPaymentMethods.css("left", x + "px");
            wooPaymentMethods.css("width", avfPaymentContainer.width());
            wooPaymentMethods.css("z-index", '1');

            $('.wc_payment_method').removeClass('selected');
            const selectedMethod = $('input[name="payment_method"]:checked');
            if (selectedMethod) {
                selectedMethod.parent().parent().toggleClass('selected');
                self.relocateBankAttachment();
            }
        } else {
            wooPaymentMethods.hide();
            self.fileUploader.hide();
        }
    }

    observePaymentMethods() {
        const self = this;

        // render into custom container
        const avfPaymentMethodClass = 'avf-payment-method-header';
        self.body
            .find(".wc_payment_method")
            .each(function () {
                if (!$(this).find(`.${avfPaymentMethodClass}`).length) {
                    $(this).prepend(`<div class="${avfPaymentMethodClass}"></div>`);
                    $(this).find(`.${avfPaymentMethodClass}`).prepend($(this).find("> label"));
                    $(this).find(`.${avfPaymentMethodClass}`).prepend('<div class="avf-payment-method-checkbox"></div>');
                    $(this).find(`.${avfPaymentMethodClass}`).prepend($(this).find("> input"));
                }
            });

        self.relocatePayments();
    }

    observeOrderSummary() {
        // subtotal
        const wooSubtotal = $(".review-order-product-item-price");
        if (wooSubtotal.length) {
            let subTotal = 0;
            wooSubtotal.each(function () {
                let itemQuantity = $(this).data("item-quantity");
                if (itemQuantity <= 0) {
                    itemQuantity = 1;
                }
                subTotal += parseFloat($(this).text().replace(/[^\d.-]/g, "")) * itemQuantity;
            });
            $("#avf_subtotal").text(
                $(".order-total .woocommerce-Price-currencySymbol").eq(0).text() +
                " " +
                subTotal.toLocaleString("en-US")
            );
        } else {
            $("#avf_subtotal").text($(".cart-subtotal bdi").text());
        }

        // shipping cost
        const wooSelectedShipping = $("#shipping_method").find("input:checked").parents("li").find("label");
        if (wooSelectedShipping.length) {
            $("#avf_shipping_selected").text(wooSelectedShipping.text());
        } else {
            $("#avf_shipping_selected").text(0);
        }

        // tax
        const wooTaxes = $(".tax-rate .woocommerce-Price-amount");
        if (wooTaxes.length) {
            $("#avf_taxes").text(wooTaxes.text());
        } else {
            $("#avf_taxes").text(0);
        }

        // discount
        const wooDiscount = $(".cart-discount"),
            avfDiscount = $("#avf_discount");
        avfDiscount.html("");
        if (wooDiscount.length) {
            wooDiscount.each(function () {
                avfDiscount.prepend(`<div>${$(this).html()}</div>`);
            });
        } else {
            avfDiscount.text(0);
        }

        // total
        $("#avf_total").text($(".order-total bdi").text());
    }

    executeObserve() {
        const self = this;
        self.observeShippingMethods();
        self.observePaymentMethods();
        self.relocateBankAttachment();
        self.observeOrderSummary();
    }

    initObserve() {
        const self = this;

        this.body.on("init_checkout", function () {
            console.log('init_checkout');
        });
        this.body.on("checkout_error", function () {
            console.log('checkout_error');
        });
        this.body.on("update_checkout", function () {
            console.log('update_checkout');
            self.avfCheckoutLoader.addClass("show");
        });
        this.body.on("updated_checkout", function () {
            console.log('updated_checkout');
            self.executeObserve();
            self.avfCheckoutLoader.removeClass("show");
            if (self.avfShippingMapClicked) {
                self.avfShippingMapClicked = false;
                if (self.differentBillingAddress) {
                    self.avfBillingCountry.val(self.wooShippingCountry.val());
                    self.avfBillingState.val(self.wooShippingState.val());
                    self.avfBillingCity.val(self.wooShippingCity.val());
                    self.avfBillingDistrict.val(self.wooShippingAddress2.val());
                    self.avfBillingAddress.val(self.wooShippingAddress1.val());
                    self.avfBillingZip.val(self.wooShippingZip.val());
                } else {
                    self.avfBillingCountry.val(self.wooBillingCountry.val());
                    self.avfBillingState.val(self.wooBillingState.val());
                    self.avfBillingCity.val(self.wooBillingCity.val());
                    self.avfBillingDistrict.val(self.wooBillingAddress2.val());
                    self.avfBillingAddress.val(self.wooBillingAddress1.val());
                    self.avfBillingZip.val(self.wooBillingZip.val());
                }
            }
        });

        this.body.on("payment_method_selected", function () {
            console.log('payment_method_selected');
            setTimeout(() => {
                self.relocatePayments();
            }, 200);
        });

        this.body.on("applied_coupon_in_checkout", function () {
            console.log('applied_coupon_in_checkout');
        });
        this.body.on("removed_coupon_in_checkout", function () {
            console.log('removed_coupon_in_checkout');
        });

        ['scroll', 'resize'].map((event) => {
            window.addEventListener(event, function () {
                self.relocatePayments();
                self.relocateBankAttachment();
            });
        });
    }
}
