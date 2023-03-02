define(["jquery"], function ($) {
    "use strict";

    return function (config) {
        let SaveURL = config.SaveURL;
        let customerId = config.customerId;
        
        $(document).on("click", "#saveCyberData", function (e) {
            e.preventDefault();

            let userId = $("#userId").val();
            let terminalId = $("#terminalID").val();
            let merchantId = $("#merchantID").val();

            if (userId != "" && terminalId != "" && merchantId != "") {
                $.ajax({
                    url: SaveURL,
                    data: {
                        customerId: customerId,
                        userId: userId,
                        terminalId: terminalId,
                        merchantId: merchantId,
                    },
                    type: "POST",
                    dataType: "json",
                    showLoader: true,
                }).done(function (data) {
                    if (data.success) {
                        $("#success-msg").show();
                    }
                }).fail(function (error) {
                    console.log(error);
                });
                $("#error-msg").hide();
            } else {
                $("#error-msg").show();
            }
        });
    };
});
