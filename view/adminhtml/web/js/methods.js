require(
    [
        'jquery',
        'mage/translate',
    ],
    function ($) {
        function processMethodSelect(currentShipperSelect) {
            // let currentShipperSelect = $(this);
            let methodSelect = currentShipperSelect.parent().parent().find('[name]').filter(function(){
                return /^groups\[allowed_shippers\]\[fields\]\[shippers\]\[value\]\[\w+\]\[method\]$/.test($(this).attr('name'));
            });
            methodSelect.attr('disabled', true);
            if (currentShipperSelect.val() !== '0') {
                fetch('/index.php/zingybits_balikobot/index/methods?shipper=' + currentShipperSelect.val())
                .then(response => response.json())
                .then(data => {
                    let methodValue = methodSelect.val();
                    methodSelect.empty();
                    if (data.length === 0) {
                        methodSelect.append('<option value="default" selected="selected">Default</option>')
                        methodSelect.attr('disabled', true);
                    } else {
                        console.log(methodValue);
                        for (const key in data) {
                            const element = data[key];
                            methodSelect.append('<option value="' + key + '"' + (methodValue === key ? ' selected="selected"' : '') + '>' + data[key] + '</option>')
                        }
                        methodSelect.attr('disabled', false);
                    }

                    // fillMethodSelect(data);
                })
                .catch(error => {
                    console.error('Error: ' + error);
                });
            }
        }

        let balikobotShippersSelects = $('[name]').filter(function(){
            return /^groups\[allowed_shippers\]\[fields\]\[shippers\]\[value\]\[\w+\]\[balikobot_shippers\]$/.test($(this).attr('name'));
        });
        balikobotShippersSelects.each(function() {
            let currentShipperSelect = $(this);
            processMethodSelect(currentShipperSelect);

            $(document).on('change', '#' + currentShipperSelect.attr('id'), function() {
                processMethodSelect($(this));
            })
        });
    }
);
