function zingybits_balikobot_add_balik()
{
    jQuery('body').loader('show');
    let orderIdInput = document.querySelector('input[name="order_id"]');
    if (orderIdInput) {
        let orderId = orderIdInput.value;
        fetch('/index.php/zingybits_balikobotcore/index/index?order_id=' + orderId)
            .then(response => response.json())
            .then(data => {
                if(data.status == "error") {
                    // TODO change to native alert
                    alert(data.message);
                } else if (data.label_url !== undefined) {
                    document.getElementById("pdf").src = data.label_url;
                    jQuery("#modal").click();
                }
                jQuery('body').loader('hide');
            })
            .catch(error => {
                console.log('Error: ' + error);
                jQuery('body').loader('hide');
            });
    } else {
        console.log('ZingyBits_Balikobot: can\'t find order_id input');
        jQuery('body').loader('hide');
    }
}



