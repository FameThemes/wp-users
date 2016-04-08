//
// Use internal $.serializeArray to get list of form elements which is
// consistent with $.serialize
//
// From version 2.0.0, $.serializeObject will stop converting [name] values
// to camelCase format. This is *consistent* with other serialize methods:
//
//   - $.serialize
//   - $.serializeArray
//
// If you require camel casing, you can either download version 1.0.4 or map
// them yourself.
//

(function($) {
    $.fn.serializeObject = function () {
        "use strict";

        var result = {};
        var extend = function (i, element) {
            var node = result[element.name];

            // If node with same name exists already, need to convert it to an array as it
            // is a multi-value field (i.e., checkboxes)

            if ('undefined' !== typeof node && node !== null) {
                if ($.isArray(node)) {
                    node.push(element.value);
                } else {
                    result[element.name] = [node, element.value];
                }
            } else {
                result[element.name] = element.value;
            }
        };

        $.each(this.serializeArray(), extend);
        return result;
    };
})(jQuery);


(function ( $ ) {

    window.WP_Users = window.WP_Users || {};

    window.WP_Users = $.extend({
        init: function(){
            var wpu = this;
            wpu.events_handle();
            wpu.forms();
            wpu.profile_avatar();
            wpu.profile_cover();

        },

        events_handle: function(){

            var wpu = this;
            wpu.hide_all_errors();

            // force reset password form
            /*
            if ( WP_Users.current_action == 'rp' ) {
                $('.wpu-modal').addClass('is-visible');
                $('#wpu-login').removeClass('is-selected');
                console.log( $('#wpu-change-password') );
                $('#wpu-change-password').addClass('is-selected');
            }
            */

            // When click to login, singup button
            $('body').on( 'click', '.wpu-singup-btn, .wpu-login-btn', function( event ) {

                var target = $( event.target );
                var is_login = target.is('.wpu-login-btn');

                if ( is_login  ) {
                    if ( target.data('is-logged') ) {
                        return true;
                    }
                }

                $('.wpu-modal').addClass('is-visible');
                $('body').trigger('wp_users_before_open');
                if ( is_login ) {
                    wpu.login_selected();
                } else {
                    wpu.signup_selected();
                }

                return false;
            } );

            //close modal when clicking the esc keyboard button
            $( document ).keyup(function(event) {
                if (event.which=='27') {
                    wpu.close_modal();
                }
            });

            // When click to overlay
            $('body').on('click', '.wpu-modal', function(event) {
                var $form_modal = $('.wpu-modal' );
                if ( $(event.target).is($form_modal) || $(event.target).is('.wpu-close-form') ) {
                    wpu.close_modal();
                }
            });

            // When click X Button
            $('body').on('click', '.wpu-modal .wpu-close-form', function(event) {
                wpu.close_modal();
            });

            // When click to Register button/link in Modal
            $( 'body' ).on( 'click', '.wpu-modal .wpu-register-link', function( event ) {
                event.preventDefault();
                wpu.signup_selected();
            } );

            // When click to Login button/link on Modal
            $('body').on( 'click', '.wpu-modal .wpu-login-link', function( event ) {
                event.preventDefault();
                wpu.login_selected();
            } );

            //Hide or show password
            $('body' ).on('click', '.wpu-pwd-toggle .hide-password', function( event ) {
                event.preventDefault();
                var $this= $(this),
                    p = $this.parent(),
                    $password_field = $('input', p );

                if ( 'password' == $password_field.attr('type') ) {
                    $password_field.attr('type', 'text');
                    $this.text( WP_Users.hide_txt );
                } else {
                    $password_field.attr('type', 'password');
                    $this.text( WP_Users.show_txt );
                }
                //focus and move cursor to the end of input field
                var v =  $password_field.val();
                var l = v.length;
                $password_field.focus();
                $password_field[0].setSelectionRange(l, l);
            });

            //Show forgot-password form
            $('body').on('click', '.wpu-modal .wpu-lost-pwd-link', function(event) {
                event.preventDefault();
                wpu.forgot_password_selected();
            });

            //back to login from the forgot-password form
            $('body').on('click', '.wpu-modal .wpu-back-to-login', function(event) {
                event.preventDefault();
                wpu.login_selected();
            });

            // Back to login Link
            if ( $('.wpu-register-form' ).hasClass('in-wpu-modal') ) {
                $( 'body' ).on( 'click', '.wpu-modal .wpu-login-link', function( event ) {
                    event.preventDefault();
                    wpu.login_selected();
                });
            }

        },
        /**
         * Remove modal
         */
        close_modal: function(){
            $('.wpu-modal' ).removeClass('is-visible');
        },
        /**
         * Switch to login tab in modal
         */
        login_selected: function() {
            var $form_modal = $('.wpu-modal' ),
                $form_login = $form_modal.find('#wpu-login'),
                $form_signup = $form_modal.find('#wpu-signup'  ),
                $form_forgot_password = $form_modal.find('#wpu-reset-password'),
                $form_change_password = $form_modal.find('#wpu-change-password'),
                $login_link = $form_modal.find( '.wpu-login-link' ),
                $signup_link = $form_modal.find( '.wpu-register-link' );

            $form_login.addClass('is-selected');
            $form_signup.removeClass('is-selected');
            $form_forgot_password.removeClass('is-selected');
            $form_change_password.removeClass('is-selected');
            $login_link.addClass('selected');
            $signup_link.removeClass('selected');
        },
        /**
         * Switch to Signup tab in modal
         */
        signup_selected: function () {

            var $form_modal =  $('.wpu-modal' ),
                $form_login = $form_modal.find('#wpu-login'),
                $form_signup = $form_modal.find('#wpu-signup'  ),
                $form_forgot_password = $form_modal.find('#wpu-reset-password'),
                $form_change_password = $form_modal.find('#wpu-change-password'),
                $login_link = $form_modal.find( '.wpu-login-link' ),
                $signup_link = $form_modal.find( '.wpu-register-link' );

            $form_login.removeClass('is-selected');
            $form_signup.addClass('is-selected');
            $form_forgot_password.removeClass('is-selected');
            $form_change_password.removeClass('is-selected');
            $login_link.removeClass('selected');
            $signup_link.addClass('selected');
        },
        /**
         * Switch to forgot pwd tab in modal
         */
        forgot_password_selected: function () {
            var $form_modal =  $('.wpu-modal' ),
                $form_login = $form_modal.find('#wpu-login'),
                $form_signup = $form_modal.find('#wpu-signup'  ),
                $form_forgot_password = $form_modal.find('#wpu-reset-password'),
                $form_change_password = $form_modal.find('#wpu-change-password');

            $form_login.removeClass('is-selected');
            $form_signup.removeClass('is-selected');
            $form_change_password.removeClass('is-selected');
            $form_forgot_password.addClass('is-selected');
        },
        /**
         * Hide all error on inputs
         */
        hide_all_errors: function () {
            // hide all errors fields when load
            $( 'body' ).on( 'click', '.wpu-form .fieldset', function( ) {
                var p = $(this);
                p.find('input').removeClass('has-error');
                p.find('span').removeClass('is-visible');
            });
        },
        /**
         * Avatar action
         */
        profile_avatar: function(){
            var wpu = this;
            var html =  '<div class="wpu-media">' +
                '<div class="cp-btn"><i class="cp-icon"></i><span>'+WP_Users.avatar_text+'</span> </div>' +
                '<div class="cp-actions"> ' +
                '<div class="cp-upload" id="profile">'+WP_Users.upload_text+'</div> ' +
                '<div class="cp-remove">'+WP_Users.remove_text+'</div> ' +
                '</div> ' +
                '</div>';

            $( '.wpu-profile-avatar[data-change="true"]').each( function(){
                var obj = $( html );
                $( this ).append( obj );
                wpu.handle_upload( 'avatar', obj );

                // Remove avatar
                $( '.cp-remove', obj).on( 'click', function(){
                    $.post( WP_Users.ajax_url, {
                        _wpnonce: WP_Users._wpnonce,
                        act: "remove_media",
                        media_type: 'avatar',
                        action: "wp_users_ajax"
                    }, function () {

                        var _default =  $( '.wpu-profile-avatar').attr( 'data-default' ) || '';
                        if ( _default !== '' ) {
                            $( '.wpu-profile-avatar').css( { backgroundImage: 'url("'+ _default +'")'} );
                        } else {
                            $( '.wpu-profile-avatar').css( { backgroundImage: ''} );
                        }

                    } );
                    $( '.cp-actions', obj ).removeClass('cp-active');
                    return false;
                } );

            } );
        },
        /**
         * Cover action
         */
        profile_cover: function(){

            var wpu = this;
            var html =  '<div class="wpu-media">' +
                '<div class="cp-btn"><i class="cp-icon"></i><span>'+WP_Users.cover_text+'</span> </div>' +
                '<div class="cp-actions"> ' +
                '<div class="cp-upload" id="profile">'+WP_Users.upload_text+'</div> ' +
                '<div class="cp-remove">'+WP_Users.remove_text+'</div> ' +
                '</div> ' +
                '</div>';

            $( '.wpu-profile-cover[data-change="true"]').each( function(){
                var obj = $( html );
                $( this ).append( obj );
                wpu.handle_upload( 'cover', obj );

                // Remove avatar
                $( '.cp-remove', obj).on( 'click', function( e ){
                    e.preventDefault();
                    $.post( WP_Users.ajax_url, {
                        _wpnonce: WP_Users._wpnonce,
                        act: "remove_media",
                        media_type: 'cover',
                        action: "wp_users_ajax"
                    }, function () {
                        var _default =  $( '.wpu-profile-cover').attr( 'data-default' ) || '';
                        if ( _default !== '' ) {
                            $( '.wpu-profile-cover').css( { backgroundImage: 'url("'+ _default +'")'} );
                        } else {
                            $( '.wpu-profile-cover').css( { backgroundImage: ''} );
                        }

                    } );
                    $( '.cp-actions', obj ).removeClass('cp-active');
                } );

            } );

        },

        handle_upload: function( type, obj ){
            var wpu = this;

            $( document ).mouseup( function ( e ) {
                if (!obj.is(e.target) // if the target of the click isn't the container...
                    && obj.has(e.target).length === 0) // ... nor a descendant of the container
                {
                    $( '.cp-actions', obj ).removeClass('cp-active');
                }
            });

            // Drop downmenu
            $( '.cp-btn', obj ).on( 'click', function(){
                $( '.cp-actions', obj ).toggleClass('cp-active');
            });

            // Upload image
            $( '.cp-upload', obj ).on( 'click', function(){
                wpu._upload_form( type );
            });
        },

        _upload_form: function( type ){
            var form;
            if ( $( '#wpu_form_update_'+type ).length > 0 ) {
                $( '#wpu_form_update_'+type).remove();
            }

            form = $( '<form style="display:none;" method="post" id="wpu_form_update_'+type+'" enctype="multipart/form-data" />' );
            form.append( '<input type="hidden" name="_wpnonce" value="'+WP_Users._wpnonce+'"/>' );
            form.append( '<input type="hidden" name="act" value="update_'+type+'"/>' );
            form.append( '<input type="hidden" name="action" value="wp_users_ajax"/>' );
            form.append( '<input type="hidden" name="redirect_url"/>' );
            form.append( '<input type="file" name="img" class="upload-file">' );
            $( 'body').append( form );
            $( 'input[name="redirect_url"]').val( window.location );
            form.attr( 'action' , WP_Users.ajax_url );
            $( '.upload-file', form).trigger( 'click' );
            $( '.upload-file', form).on( 'change', function(){
                var file = $( '.upload-file', form).val();
                var ext = file.match(/\.(.+)$/)[1];
                switch (ext) {
                    case 'jpg':
                    case 'jpeg':
                    case 'png':
                    case 'gif':
                        break;
                    default:
                        alert( WP_Users.invalid_file_type );
                        return false;
                }

                form.submit();
            } );

        },
        /**
         * Handle forms event
         */
        forms: function() {
            var wpu = this;
            /**
             * Login form submit
             */
            $('.wpu-login-form' ).submit( function() {
                //return false;
                var form = $(this);
                var formData = form.serializeObject();
                formData.action = 'wp_users_ajax';
                formData.act = 'do_login';
                form.addClass( 'wpu-loading loading' );
                $( '.wpu-error-msg', form ).removeClass( 'is-visible' );
                $( '.has-error', form ).removeClass( 'has-error' );

                $.ajax({
                    url: WP_Users.ajax_url,
                    data: formData,
                    type: 'POST',
                    success: function( response ) {
                        form.removeClass( 'wpu-loading loading' );
                        if ( response === 'logged_success' ) {
                            var redirect_url = ( typeof formData.wpu_redirect_to !== undefined  & formData.wpu_redirect_to != '' ) ? formData.wpu_redirect_to : window.location;
                            window.location = redirect_url;
                            return ;
                        } else {
                            try {
                                var res = JSON.parse( response);
                                $.each( res, function ( k , msg ) {
                                    if ( $( '.'+k , form).length > 0 ) {
                                        var p = $( '.'+k , form);
                                        if ( $('.wpu-error-msg', p ).length <= 0 ) {
                                            p.append( '<span class="wpu-error-msg"></span>' );
                                        }
                                        $('.wpu-error-msg', p).html( msg).addClass( 'is-visible' );
                                        p.find('input').toggleClass('has-error');
                                    }
                                } );
                            } catch ( e ) {

                            }

                        }
                    }
                });
                return false;
            } );


            /**
             * Register form submit
             */
            $('.wpu-register-form' ).submit( function() {

                var form = $( this );
                var formData = form.serializeObject();
                formData.action = 'wp_users_ajax';
                formData.act = 'do_register';

                if ( $('input[name="wpu_accept_terms"]:checked', form ).length == 0  ) {
                    $('.accept-terms .wpu-error-msg' , form ).toggleClass('is-visible');
                }

                var submit_btn =  $('input[type="submit"], button[type="submit"]', form);
                var txt = submit_btn.val();
                submit_btn.data('default-text', txt );
                if ( submit_btn.data('loading-text') !== '' ) {
                    submit_btn.val( submit_btn.data('loading-text') ) ;
                    submit_btn.attr('disabled', 'disabled');
                }

                form.addClass( 'wpu-loading loading' );
                $( '.wpu-error-msg', form ).removeClass( 'is-visible' );
                $( '.has-error', form ).removeClass( 'has-error' );

                $.ajax({
                    url: WP_Users.ajax_url,
                    data: formData,
                    type: 'POST',
                    success: function( response ) {
                        form.removeClass( 'wpu-loading loading' );
                        submit_btn.val( submit_btn.data('default-text') ) ;
                        submit_btn.removeAttr('disabled');

                        if ( ! isNaN( response ) ) { // success - user created.

                            var redirect_url = ( typeof formData.wpu_redirect_to !== 'undefined'  & formData.wpu_redirect_to != '' ) ? formData.wpu_redirect_to : window.location;
                            $('input[type=text], input[type=email], input[type=password], input[type=number]',form).val('');
                            $('input[type=checkbox]',form).removeAttr('checked');
                            $('.wpu-msg',form).show(0);

                            return ;
                        } else {

                            try {
                                var res = JSON.parse( response);
                                $.each( res, function ( k , msg ) {
                                    if ( $( '.'+k , form).length > 0 ) {
                                        var p = $( '.'+k , form);
                                        if ( $('.wpu-error-msg', p ).length <= 0 ) {
                                            p.append( '<span class="wpu-error-msg"></span>' );
                                        }
                                        $('.wpu-error-msg', p).html( msg ).addClass( 'is-visible' );
                                        p.find('input').toggleClass('has-error');
                                    }
                                } );
                            } catch ( e ) {

                            }
                        }
                    }
                });
                return false;
            } );

            // Lost pwd form submit
            $('.wpu-form-reset-password' ).submit( function() {
                var form = $(this);
                var formData = form.serializeObject();
                formData.action = 'wp_users_ajax';
                formData.act = 'retrieve_password';

                var submit_btn =  $('input[type="submit"], button[type="submit"]', form);
                var txt = submit_btn.val();
                submit_btn.data('default-text', txt );
                if ( submit_btn.data('loading-text') !== '' ) {
                    submit_btn.val( submit_btn.data('loading-text') ) ;
                    submit_btn.attr('disabled', 'disabled');
                }

                form.addClass( 'wpu-loading loading' );
                $( '.wpu-error-msg', form ).removeClass( 'is-visible' );
                $( '.has-error', form ).removeClass( 'has-error' );

                $.ajax({
                    url: WP_Users.ajax_url,
                    data: formData,
                    type: 'POST',
                    success: function( response ) {
                        form.removeClass( 'wpu-loading loading' );
                        submit_btn.val( submit_btn.data('default-text') ) ;
                        submit_btn.removeAttr('disabled');
                        if ( response == 'sent' ) {
                            $( 'input[type=text], input[type=email], input[type=password], input[type=number]',form).val('');
                            $( 'input[type=checkbox]',form ).removeAttr('checked');
                            $( '.wpu-msg', form ).show(100);
                        } else {

                            try {
                                var res = JSON.parse( response);
                                $.each( res, function ( k , msg ) {
                                    if ( $( '.'+k , form).length > 0 ) {
                                        var p = $( '.'+k , form);
                                        if ( $('.wpu-error-msg', p ).length <= 0 ) {
                                            p.append( '<span class="wpu-error-msg"></span>' );
                                        }
                                        $('.wpu-error-msg', p).html( msg ).addClass( 'is-visible' );
                                        p.find('input').toggleClass('has-error');
                                    }
                                } );
                            } catch ( e ) {
                                $('.wpu-msg', form ).addClass('has-error').html( response).show();
                            }
                        }
                    }
                });

                return false;
            } );

            // change pwd form submit
            $('.wpu-form-change-password').submit( function() {

                var form = $(this);
                var formData = form.serializeObject();
                formData.action = 'wp_users_ajax';
                formData.act = 'do_reset_pass';

                var submit_btn =  $('input[type="submit"], button[type="submit"]', form);
                var txt = submit_btn.val();
                submit_btn.data('default-text', txt );
                if ( submit_btn.data('loading-text') !== '' ) {
                    submit_btn.val( submit_btn.data('loading-text') ) ;
                    submit_btn.attr('disabled', 'disabled');
                }

                $('.wpu-msg', form).hide(1);
                form.addClass( 'wpu-loading loading' );
                $( '.has-error', form ).removeClass( 'has-error' );

                $.ajax({
                    url: WP_Users.ajax_url,
                    data: formData,
                    type: 'POST',
                    success: function( response ) {
                        form.removeClass( 'wpu-loading loading' );
                        submit_btn.val( submit_btn.data('default-text') ) ;
                        submit_btn.removeAttr('disabled');

                        if ( response == 'changed' ) {
                            $('input[type=text], input[type=email], input[type=password], input[type=number]',form).val('');
                            $('.wpu-msg', form).show(1);
                        } else {

                            try {
                                var res = JSON.parse( response );
                                $('.wpu-msg', form).hide(1);
                                if ( typeof res.error !== 'undefined'  && res.error !=='' ) {
                                    $('.wpu-errors-msg', form).html(res.error).show(1);
                                }
                                $.each( res, function ( key, value ) {
                                    var  p = $('.' + key, form );
                                    $('.wpu-error-msg', p).html( value ).toggleClass('is-visible');
                                    p.find('.input').toggleClass('has-error');
                                } );
                            } catch ( e ){

                            }

                        }
                    }
                });

                return false;
            } );

            // Profile Submit
            $( 'form.wpu-form-profile' ).submit( function() {

                var form = $(this);
                var formData = form.serializeObject();
                formData.action = 'wp_users_ajax';
                formData.act = 'do_update_profile';
                var submit_btn =  $('input[type="submit"], button[type="submit"]', form);
                var txt = submit_btn.val();
                submit_btn.data('default-text', txt );
                if ( submit_btn.data('loading-text') !== '' ) {
                    submit_btn.val( submit_btn.data('loading-text') ) ;
                    submit_btn.attr('disabled', 'disabled');
                }

                $('.wpu-msg', form).hide(1);

                form.addClass( 'wpu-loading loading' );

                $.ajax({
                    url: WP_Users.ajax_url,
                    data: formData,
                    type: 'POST',
                    success: function( response ) {

                        form.removeClass( 'wpu-loading loading' );

                        submit_btn.val( submit_btn.data('default-text') ) ;
                        submit_btn.removeAttr('disabled');

                        if ( response == 'updated' ) {
                            var c_url = window.location.href;
                            if ( c_url.indexOf("wpu_profile_updated") == -1 ) {
                                if (c_url.indexOf("?") !== -1) {
                                    c_url += '&wpu_profile_updated=1';
                                } else {
                                    c_url += '?wpu_profile_updated=1';
                                }
                            }

                            // refresh page
                            window.location =  c_url;
                            $('.wpu-msg', form).show(1);
                        } else {
                            try {
                                var res = JSON.parse( response );
                                $('.wpu-msg', form).hide(1);
                                if ( typeof res.error !== 'undefined'  && res.error !=='' ) {
                                    $('.wpu-errors-msg', form).html(res.error).show(1);
                                }
                                $.each( res, function ( key, value ) {
                                    var  p = $('.' + key, form );
                                    $('.wpu-error-msg', p).html( value ).toggleClass('is-visible');
                                    p.find('.input').toggleClass('has-error');
                                } );
                            } catch ( e ) {

                            }

                        }
                    }
                });

                return  false;
            } );


        } /* END init */

    }, window.WP_Users );


    $.fn.wp_users = function( ) {
        return this.each( function(){
           return window.WP_Users.init();
        } );
    };

    $( 'body').wp_users();

}( jQuery ));


