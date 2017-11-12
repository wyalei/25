function deleteStyle(type, id, name) {
    if(confirm("你确定要删除" + name +"吗？")){
        location.href = "__URL__/home?type_link=" + type + "&handle=delete&id=" + id;
    }
}

function modifyStyleName(type, id, name) {
    var new_name = prompt("请输入新名字", name);
    if(new_name != name){
        location.href = "__URL__/home?type_link=" + type + "&handle=modify&id=" + id + "&name=" + new_name;
    }
}

function modifyStyleOrder(type, id, order) {
    var new_order = prompt("请输入新顺序", order);
    if(new_order != order){
        location.href = "__URL__/home?type_link=" + type +"&handle=modify&id=" + id + "&order=" + new_order;
    }
}