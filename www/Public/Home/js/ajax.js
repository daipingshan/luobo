//学历删除
function delete_course(id){
    return true;
    /* 数据提交*/
            var request = $.ajax({
                url: "test",
                method: "POST",
                data: {
                   id : id
                },
                dataType: "json",
                headers: {
                    
                }
            });

            request.done(function (msg) {
                return true;
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(textStatus);
                return false;
            });
}

//学历删除
function delete_works(id){
    return true;
    /* 数据提交*/
            var request = $.ajax({
                url: "delete_works",
                method: "POST",
                data: {
                   id : id
                },
                dataType: "json",
                headers: {
                    
                }
            });

            request.done(function (msg) {
                return true;
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(textStatus);
                return false;
            });
}

//学历删除
function delete_curriculum_vitae(id){
    return true;
    /* 数据提交*/
            var request = $.ajax({
                url: "delete_curriculum_vitae",
                method: "POST",
                data: {
                   id : id
                },
                dataType: "json",
                headers: {
                    
                }
            });

            request.done(function (msg) {
                return true;
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(textStatus);
                return false;
            });
}

//更换简历投递方式
function change_vitae_type(vitaeType){
    /* 测试用 */
    if(vitaeType == 'word'){
        return true;
    }
    /* end 测试用*/
    /* 数据提交*/
            var request = $.ajax({
                url: "delete_curriculum_vitae",
                method: "POST",
                data: {
                   vitaeType : vitaeType
                },
                dataType: "json",
                headers: {
                    
                }
            });

            request.done(function (msg) {
                return true;
            });

            request.fail(function (jqXHR, textStatus) {
                console.log(textStatus);
                return false;
            });
}