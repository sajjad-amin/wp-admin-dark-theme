(function () {
    tinymce.PluginManager.add('wpadttoggle', function (editor, url) {
        editor.addButton('wpadttoggle', {
            title: 'Editor Display UI (Working Settings)',
            text: 'UI Color/Size', // Fallback text when icons fail
            icon: 'wp_code', // Uses the standard WP code icon if available
            onclick: function () {
                var size = window.wpadtSettings ? window.wpadtSettings.editor_text_size : '16';
                var tColor = window.wpadtSettings ? window.wpadtSettings.editor_text_color : '#d1dbe5';
                var bColor = window.wpadtSettings ? window.wpadtSettings.editor_bg_color : '#2b3641';

                var defaultSize = '16';
                var defaultTColor = '#d1dbe5';
                var defaultBColor = '#2b3641';

                var win = editor.windowManager.open({
                    title: 'Editor Working UI Settings',
                    width: 450,
                    height: 280,
                    body: [
                        {
                            type: 'container',
                            html: '<div style="padding: 10px 20px; color: var(--adt-text-main, #d1dbe5);">' +

                                '<div style="margin-bottom:20px;">' +
                                '<label style="display:block;margin-bottom:8px;font-weight:bold;">Text Size: <span id="wpadt-size-val">' + size + '</span>px</label>' +
                                '<input type="range" id="wpadt_size_slider" min="10" max="72" value="' + size + '" oninput="document.getElementById(\'wpadt-size-val\').innerText=this.value;" style="width:100%;">' +
                                '</div>' +

                                '<div style="margin-bottom:20px;">' +
                                '<label style="display:block;margin-bottom:8px;font-weight:bold;">Text Color</label>' +
                                '<div style="display:flex; align-items:center; gap:10px;">' +
                                '<input type="color" id="wpadt_tc_picker" value="' + tColor + '" oninput="document.getElementById(\'wpadt_tc_hex\').value=this.value;" style="height:35px; width:45px; cursor:pointer; padding:0; border:1px solid #3e4e5e; border-radius:3px;" />' +
                                '<input type="text" id="wpadt_tc_hex" value="' + tColor + '" oninput="document.getElementById(\'wpadt_tc_picker\').value=this.value;" style="flex:1; padding:8px; background:var(--adt-bg-dark, #222b34); color:var(--adt-text-heading, #f0f5fa); border:1px solid var(--adt-border, #3e4e5e); border-radius:3px;">' +
                                '</div>' +
                                '</div>' +

                                '<div style="margin-bottom:10px;">' +
                                '<label style="display:block;margin-bottom:8px;font-weight:bold;">Background Color</label>' +
                                '<div style="display:flex; align-items:center; gap:10px;">' +
                                '<input type="color" id="wpadt_bc_picker" value="' + bColor + '" oninput="document.getElementById(\'wpadt_bc_hex\').value=this.value;" style="height:35px; width:45px; cursor:pointer; padding:0; border:1px solid #3e4e5e; border-radius:3px;" />' +
                                '<input type="text" id="wpadt_bc_hex" value="' + bColor + '" oninput="document.getElementById(\'wpadt_bc_picker\').value=this.value;" style="flex:1; padding:8px; background:var(--adt-bg-dark, #222b34); color:var(--adt-text-heading, #f0f5fa); border:1px solid var(--adt-border, #3e4e5e); border-radius:3px;">' +
                                '</div>' +
                                '</div>' +

                                '</div>'
                        }
                    ],
                    buttons: [
                        {
                            text: 'Reset to Defaults',
                            onclick: function () {
                                document.getElementById('wpadt_size_slider').value = defaultSize;
                                document.getElementById('wpadt-size-val').innerText = defaultSize;
                                document.getElementById('wpadt_tc_picker').value = defaultTColor;
                                document.getElementById('wpadt_tc_hex').value = defaultTColor;
                                document.getElementById('wpadt_bc_picker').value = defaultBColor;
                                document.getElementById('wpadt_bc_hex').value = defaultBColor;
                            }
                        },
                        {
                            text: 'Cancel',
                            onclick: 'close'
                        },
                        {
                            text: 'Apply Settings',
                            subtype: 'primary',
                            onclick: 'submit'
                        }
                    ],
                    onsubmit: function (e) {
                        var sliderEl = document.getElementById('wpadt_size_slider');
                        var tcHexEl = document.getElementById('wpadt_tc_hex');
                        var bcHexEl = document.getElementById('wpadt_bc_hex');

                        var newSize = sliderEl ? sliderEl.value : size;
                        var newTextColor = tcHexEl ? tcHexEl.value : tColor;
                        var newBgColor = bcHexEl ? bcHexEl.value : bColor;

                        // Apply to editor instantly
                        var body = editor.getBody();
                        body.style.setProperty('font-size', newSize + 'px', 'important');
                        body.style.setProperty('color', newTextColor, 'important');
                        body.style.setProperty('background-color', newBgColor, 'important');

                        // Update current settings object
                        if (window.wpadtSettings) {
                            window.wpadtSettings.editor_text_size = newSize;
                            window.wpadtSettings.editor_text_color = newTextColor;
                            window.wpadtSettings.editor_bg_color = newBgColor;

                            // Save via AJAX so it persists and matches settings page
                            jQuery.post(window.wpadtSettings.ajaxurl, {
                                action: 'wpadt_save_editor_settings',
                                nonce: window.wpadtSettings.nonce,
                                size: newSize,
                                text_color: newTextColor,
                                bg_color: newBgColor
                            });
                        }
                    }
                });
            }
        });
    });
})();
