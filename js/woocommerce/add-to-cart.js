/* global wc_add_to_cart_params */
jQuery(function ($) {

    if (typeof wc_add_to_cart_params === 'undefined') {
        return false;
    }

    /**
     * AddToCartHandler class.
     */
    var AddToCartHandler = function () {
        this.requests = [];
        this.addRequest = this.addRequest.bind(this);
        this.run = this.run.bind(this);

        $(document.body)
            .on('click', '.add_to_cart_button', { addToCartHandler: this }, this.onAddToCart)
            .on('click', '.remove_from_cart_button', { addToCartHandler: this }, this.onRemoveFromCart)
            .on('added_to_cart', this.updateButton)
            .on('ajax_request_not_sent.adding_to_cart', this.updateButton)
            .on('added_to_cart removed_from_cart', { addToCartHandler: this }, this.updateFragments);
    };

    /**
     * Add add to cart event.
     */
    AddToCartHandler.prototype.addRequest = function (request) {
        this.requests.push(request);

        if (1 === this.requests.length) {
            this.run();
        }
    };

    /**
     * Run add to cart events.
     */
    AddToCartHandler.prototype.run = function () {
        var requestManager = this,
            originalCallback = requestManager.requests[0].complete;

        requestManager.requests[0].complete = function () {
            if (typeof originalCallback === 'function') {
                originalCallback();
            }

            requestManager.requests.shift();

            if (requestManager.requests.length > 0) {
                requestManager.run();
            }
        };

        $.ajax(this.requests[0]);
    };

    /**
     * Handle the add to cart event.
     */
    AddToCartHandler.prototype.onAddToCart = function (e) {
        var $thisbutton = $(this);

        if ($thisbutton.is('.ajax_add_to_cart')) {
            if (!$thisbutton.attr('data-product_id')) {
                return true;
            }

            e.preventDefault();

            $thisbutton.removeClass('added');
            $thisbutton.addClass('loading');

            console.log($thisbutton)
            if ($thisbutton.hasClass('js-cart-dso')) {
                console.log($thisbutton.attr("data-parent-product_id"))
                localStorage.setItem($thisbutton.attr("data-parent-product_id"), $thisbutton.attr('data-product_id'));
                console.log(localStorage)
            }

            // Allow 3rd parties to validate and quit early.
            if (false === $(document.body).triggerHandler('should_send_ajax_request.adding_to_cart', [$thisbutton])) {
                $(document.body).trigger('ajax_request_not_sent.adding_to_cart', [false, false, $thisbutton]);
                return true;
            }

            var data = {};

            // Fetch changes that are directly added by calling $thisbutton.data( key, value )
            $.each($thisbutton.data(), function (key, value) {
                data[key] = value;
            });

            // Fetch data attributes in $thisbutton. Give preference to data-attributes because they can be directly modified by javascript
            // while `.data` are jquery specific memory stores.
            $.each($thisbutton[0].dataset, function (key, value) {
                data[key] = value;
            });

            // Trigger event.
            $(document.body).trigger('adding_to_cart', [$thisbutton, data]);

            e.data.addToCartHandler.addRequest({
                type: 'POST',
                url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart'),
                data: data,
                success: function (response) {
                    if (!response) {
                        return;
                    }

                    if (response.error && response.product_url) {
                        window.location = response.product_url;
                        return;
                    }

                    // Redirect to cart option
                    if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
                        window.location = wc_add_to_cart_params.cart_url;
                        return;
                    }

                    // Trigger event so themes can refresh other areas.
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
                },
                dataType: 'json'
            });
        }
    };

    /**
     * Update fragments after remove from cart event in mini-cart.
     */
    AddToCartHandler.prototype.onRemoveFromCart = function (e) {
        var $thisbutton = $(this),
            $row = $thisbutton.closest('.woocommerce-mini-cart-item');

        e.preventDefault();

        $row.block({
            message: null,
            overlayCSS: {
                opacity: 0.6
            }
        });

        e.data.addToCartHandler.addRequest({
            type: 'POST',
            url: wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'remove_from_cart'),
            data: {
                cart_item_key: $thisbutton.data('cart_item_key')
            },
            success: function (response) {
                if (!response || !response.fragments) {
                    window.location = $thisbutton.attr('href');
                    return;
                }

                $(document.body).trigger('removed_from_cart', [response.fragments, response.cart_hash, $thisbutton]);
            },
            error: function () {
                window.location = $thisbutton.attr('href');
                return;
            },
            dataType: 'json'
        });
    };

    /**
     * Update cart page elements after add to cart events.
     */
    AddToCartHandler.prototype.updateButton = function (e, fragments, cart_hash, $button) {
        $button = typeof $button === 'undefined' ? false : $button;

        if ($button) {
            $button.removeClass('loading');

            if (fragments) {
                $button.addClass('added');
            }

            // // View cart text.
            if (fragments && !wc_add_to_cart_params.is_cart && $button.parent().find('.added_to_cart').length === 0) {
                setTimeout(() => {
                    const removeLinks = document.querySelectorAll(".js-remove-link");
                    removeLinks.forEach(link => {
                        if (link.dataset.id === $button.attr("data-product_id")) {
                            $('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').html($('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').html().replace("В корзину", "Убрать из корзины"));
                            $('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').addClass('btn-secondary remove js-remove-from-cart-prs');
                            $('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').attr("href", link.dataset.url);
                            $('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').removeClass("btn-primary button wp-element-button product_type_simple add_to_cart_button ajax_add_to_cart added");
                            $('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').attr("data-cart_item_key", link.dataset.itemKey);
                            $('a.product__buy[data-product_id="' + $button.attr("data-product_id") + '"]').attr("aria-label", "Убрать из корзины");
                        }
                    })

                    const cartP = document.querySelector(".js-cartP")
                    const body = document.querySelector("body")
                    body.classList.add("scroll-disabled")
                    cartP.classList.add("active")
                }, 50);
            }

            $(document.body).trigger('wc_cart_button_updated', [$button]);
        }
    };

    /**
     * Update fragments after add to cart events.
     */
    AddToCartHandler.prototype.updateFragments = function (e, fragments) {
        if (fragments) {
            $.each(fragments, function (key) {
                $(key)
                    .addClass('updating')
                    .fadeTo('400', '0.6')
                    .block({
                        message: null,
                        overlayCSS: {
                            opacity: 0.6
                        }
                    });
            });

            $.each(fragments, function (key, value) {
                $(key).replaceWith(value);
                $(key).stop(true).css('opacity', '1').unblock();
            });

            $(document.body).trigger('wc_fragments_loaded');
        }
    };

    /**
     * Init AddToCartHandler.
     */
    new AddToCartHandler();

    // Ajax delete product in the cart
    $(document).on('click', 'a.js-remove-from-cart-prs', function (e) {
        e.preventDefault();

        var product_id = $(this).attr("data-product_id"),
            cart_item_key = $(this).attr("data-cart_item_key");

        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: wc_add_to_cart_params.ajax_url,
            data: {
                action: "product_remove",
                product_id: product_id,
                cart_item_key: cart_item_key
            },
            success: function (response) {
                if (!response || response.error)
                    return;

                var fragments = response.fragments;

                // Replace fragments
                if (fragments) {
                    $.each(fragments, function (key, value) {
                        $(key).replaceWith(value);
                    });
                }
            }
        });

        const btns = document.querySelectorAll(`a.product__buy[data-product_id="${product_id}"]`)

        btns.forEach(btn => {
            btn.setAttribute('href', `?add-to-cart=${product_id}`);
            btn.classList.remove("btn-secondary")
            btn.classList.remove("remove")
            btn.classList.remove("js-remove-from-cart-prs")
            btn.classList.add("btn-primary")
            btn.classList.add("button")
            btn.classList.add("wp-element-button")
            btn.classList.add("product_type_simple")
            btn.classList.add("add_to_cart_button")
            btn.classList.add("ajax_add_to_cart")
            btn.setAttribute("aria-label", "Добавить в корзину")

            btn.innerHTML = btn.innerHTML.replace("Убрать из корзины", "В корзину")
        })
    });
});
