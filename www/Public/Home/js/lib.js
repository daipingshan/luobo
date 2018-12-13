$(function(){
    $("#toTop").click(function(){
    $("html").animate({"scrollTop": "0px"},100); //IE,FF
    $("body").animate({"scrollTop": "0px"},100); //Webkit
       });
 });

$(function(){
    toastr.options.showMethod = 'slideDown';
    toastr.options.hideMethod = 'slideUp';
    toastr.options.progressBar = true;
    toastr.options.positionClass = 'toast-top-center';

});


function check_idcode(identity){
    var regIdCard = /^(^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$)|(^[1-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])((\d{4})|\d{3}[Xx])$)$/;

    if (regIdCard.test(identity)) {
        if (identity.length == 18) {
            var idCardWi = new Array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
            var idCardY = new Array(1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2);
            var idCardWiSum = 0;
            for (var i = 0; i < 17; i++) {
                idCardWiSum += identity.substring(i, i + 1) * idCardWi[i];
            }
            var idCardMod = idCardWiSum % 11;
            var idCardLast = identity.substring(17);

            if (idCardMod == 2) {
                if (idCardLast == "X" || idCardLast == "x") {

                } else {
                    toastr.info('身份证填写有误，请重新填写');
                    return false;

                }
            } else {

                if (idCardLast == idCardY[idCardMod]) {

                } else {

                    toastr.info('身份证填写有误，请重新填写');
                    return false;

                }
            }
        }
    } else {
        toastr.info('身份证填写有误，请重新填写');
        return false;
    }
    return true;

}