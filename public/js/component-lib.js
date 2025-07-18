export var compomentLib = {};
export function initComponentLib() {
    compomentLib = {
        "console": document.getElementById("console"),
        "nav": document.querySelectorAll("[data-nav-target]"),
        "assignAgent": document.querySelector("[name=assignAgent]"),
        "validTicket": document.querySelectorAll("[name=validTicket]"),
        "updateTicket": document.querySelector("[name=updateTicket]"),
        "addData": document.getElementById("addData"),
        "addAgent": document.getElementById("addAgent"),
        "seeBtn": document.querySelectorAll("[name=seeTicket]"),
        "requalif": document.querySelectorAll("[name=requalifTicket]"),
        "seeAgent": document.querySelectorAll("[name=seeAgent]"),
        "seeService":document.querySelectorAll("[name=seeService]")
    }
    if (compomentLib.nav)
        setNavButton(compomentLib.nav);
    
}
export function createSelectCompoment(values){
    let select=document.createElement("select");
    values.forEach(el=>{
        let opt=document.createElement("option");
        opt.value=el.value;
        opt.innerText=el.text;
        select.appendChild(opt);
    });
    return select;
}
export function resetTab() {
    let t = ["ticketFrom", "ticketTo"];
    t.forEach(el => {
        if (document.getElementById(el) != undefined) {
            document.getElementById(el).style.display = "none";
            document.querySelector(`[data-nav-target=${el}]`).classList.remove("active");
        }
    })
}
function setNavButton(btns) {
    btns.forEach(el => {
        el.onclick = function () {
            resetTab();
            let target = this.getAttribute("data-nav-target");
            //Check if there ticket 
            console.log(`#${target} table tbody tr`);
            if (document.querySelectorAll(`#${target} table tbody tr`).length > 0) {
                document.getElementById(target).style.display = "table";
                this.classList.add("active");
            }
            return false;
        }
    });
}