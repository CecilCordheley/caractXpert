function _alert(msg, callBack = undefined,cat=0) {
    //Overlay de l'alerte
    let classes=["normal","warning","information"]
    let container = document.createElement("div");
    container.classList.add("alert_overlay");
    container.onclick = function () {
        this.remove();
        if (callBack != undefined) {
            callBack.call();
        }
    }
    //Div du Message
    let message = document.createElement("div");
    message.classList.add(classes[cat]);
    message.innerHTML = "<p>" + msg + "</p>";
    container.appendChild(message);
    document.body.appendChild(container);
}
function _confirm(msg,success,failed,cat=0){
     //Overlay de l'alerte
    let classes=["normal","warning","information"]
    let container = document.createElement("div");
    container.classList.add("alert_overlay");
    container.onclick = function () {
        this.remove();
    }
     let message = document.createElement("div");
    message.classList.add(classes[cat]);
    message.innerHTML = "<p>" + msg + "</p>";
    container.appendChild(message);
    let footer=document.createElement("div");
    footer.classList.add("footer");
    message.appendChild(footer);
    let btnsuccess=document.createElement("button");
    btnsuccess.classList.add("btn");
    btnsuccess.classList.add("btn-primary");
    btnsuccess.innerText="OUI";
    btnsuccess.onclick=success;
    footer.appendChild(btnsuccess);
     let btnFailed=document.createElement("button");
    btnFailed.classList.add("btn");
    btnFailed.classList.add("btn-danger");
    btnFailed.innerText="NON";
    btnFailed.onclick=failed;
    footer.appendChild(btnFailed);
    document.body.appendChild(container);
}
function setTitleAttr(el) {
    let m = el.title;
    let div = document.createElement("div");
    div.classList.add("titleAttr");
    div.innerHTML = m;
    div["style"]["top"] = el.offsetTop;
    el.onmouseover = function () {
        document.body.appendChild(div);
    }
    el.onmouseout = function () {
        div.remove();
    }
}