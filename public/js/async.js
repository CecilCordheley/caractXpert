

async function useLicence(uuid, cible, type_cible, action, myParam, comment) {
    console.log("param:", myParam); // ← pour vérifier qu'il contient bien des données

    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            cible: cible,
            type_cible: type_cible,
            action: action,
            params: JSON.stringify(myParam),
            comment: comment
        })
    };

    try {
        console.log("Type de param:", typeof myParam);
        console.log("Instance de Array ?", Array.isArray(myParam));
        console.log("Contenu de param:", myParam);
        console.log("Body JSON.stringify:", JSON.stringify({ params: myParam }));
        const response = await fetch(`async/LicenceFnc_use?uuid=${uuid}`, options);
        const result = await response.text();
        console.dir(result);
    } catch (err) {
        console.error("Erreur fetch:", err);
    }
}
async function updateUser(success, failed) {
    let token = JSON.parse(localStorage.getItem("user")).token;
    let data = getFormData("#UpdateUserFrm");
    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Authorization": 'Bearer : ' + token
        },
        body: JSON.stringify(data)

    };
    const response = await fetch("async/users_update?uuid=" + data["uuidUser"], options);
    const result = await response.json();
    if (result.status.toLowerCase() == "success") {
        success?.(result.data);
    } else {
        failed?.(result.message);
    }
}
async function getAllCaracteristics(success, failed) {
    const response = await fetch("async/pannes_caracteristiks");
    const result = await response.json();
    if (result.status.toLowerCase() == "success") {
        success?.(result.data);
    } else {
        failed?.(result.message);
    }
}
async function getPanne(success, failed) {
    const response = await fetch("async/pannes_getAll?ids=1");
    const result = await response.json();
    if (result.status.toLowerCase() == "success") {
        success?.(result.data);
    } else {
        failed?.(result.message);
    }
}
async function createPassWord(mail, mdp, succes, failed) {
    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            mail: mail,
            mdp: mdp
        })

    };
    const response = await fetch(`async/users_generatePassword`, options);
    const result = await response.json();
    if (result.status.toLowerCase() == "success") {
        succes?.();
    } else {
        failed?.();
    }
}
async function addUser(success, failed) {
    let data = getFormData("#addUserFrm");
    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(data)
    };
    const response = await fetch(`async/users_add`, options);
    const result = await response.json();
    if (result.status == "success") {
        success.call(this, result.data);
    } else {
        failed.call(this, result.message);
    }

}
function executeScript(container) {
    container.querySelectorAll("script").forEach(oldScript => {
        //  console.log("set New Script " + oldScript.textContent);
        const newScript = document.createElement("script");
        if (oldScript.src) {
            newScript.src = oldScript.src;
        } else {
            newScript.textContent = oldScript.textContent;
        }
        document.head.appendChild(newScript).remove(); // Évite les doublons
    });
}
async function loadView(view, onload, onFailed) {
    try {
        let token = JSON.parse(localStorage.getItem("user")).token;
        const response = await fetch(view, {
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        });
        const result = await response.json();

        if (result.status === "success") {
            let mainInterface = document.getElementById("MainActivity");
            mainInterface.style.opacity = 0;
            setTimeout(() => {
                mainInterface.innerHTML = result.data;
                mainInterface.style.opacity = 1;
                executeScript(mainInterface);
                onload?.(); // si défini


            }, 1000);

        } else {
            if (result.code == "401") {
                _alert("Problème sur le token de connexion", function () {
                    window.location.href = "deconnexion";
                }, 1);
            }
            onFailed?.(result.message);
        }
    } catch (err) {
        onFailed?.(err);
    }
}
function createTableRow(cells) {
    const tr = document.createElement("tr");
    tr.innerHTML = cells.map(cell => `<td>${cell}</td>`).join('');
    return tr;
}
async function refreshPwd(user, success, failed) {
    let token = JSON.parse(localStorage.getItem("user")).token;
    fetch("async/users_refreshPwd?user=" + user, {
        method: "GET",
        headers: {
            "Authorization": 'Bearer : ' + token
        }
    })
        .then(r => { return r.json() })
        .then(result => {
            if (result.status.toLowerCase() == "success")
                success?.call(this);
            else
                failed?.call(result.message);
        })
}
async function hasEvent(idPanne, success, failed) {
    fetch("async/pannes_hasEvent?id=" + idPanne)
        .then(r => { return r.json(); })
        .then(result => {
            if (result.status === "success") {
                success?.call(this, result.data);
            } else {
                failed?.call(result.message);
            }
        })
}
async function getTrigger(success, failed) {
    fetch("async/event_get")
        .then(r => { return r.json() })
        .then(result => {
            if (result.status == "success") {
                success.call(this, result.data);
            } else {
                failed.call(result.message);
            }
        })
}
async function createTrigger(ref, name, content, success, failed) {
    let token = JSON.parse(localStorage.getItem("user")).token;
    const options = {
        method: "POST",
        headers: {
            "Authorization": 'Bearer : ' + token,
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "ref": ref,
            "name": name,
            "content": content
        })
    };
    const response = await fetch(`async/pannes_createTrigger`, options);
    const result = await response.json();
    if (result.status == "success") {
        success.call(this);
    } else {
        failed.call(this, result.message);
    }
}
async function getCategory(succes, failed) {
    fetch("async/pannes_getAllCategorie")
        .then(r => { return r.json() })
        .then(result => {
            if (result.status == "success") {
                succes?.call(this, result.data);
            } else {
                failed?.call(this, result.message);
            }
        })
        .catch(err => {
            failed?.call(this, err);
        })
}
async function getStat(stat, success, failed) {
    let statFnc = {
        "countByPanne": function (success,failed) {
            fetch("async/stat_countByPanne")
                .then(r => { return r.json() })
                .then(result => {
                    if (result.status == "success") {
                        success?.call(this, result.data);
                    } else {
                        failed?.call(this, result.message);
                    }
                })
                .catch(err => {
                    failed?.call(this, err);
                });
        }

    }
    if (statFnc[stat] != undefined) {
        statFnc[stat](success,failed);
    }
}
async function panneEvent() {
    loadView("async/view_panneEvent", () => {
        let panne_select_frm = document.getElementById("panne_select_frm");
        panne_select_frm.style.display = "none";
        getTrigger((events) => {
            let lst = document.getElementById("eventList");
            lst.innerHTML = "";
            events.forEach(element => {
                console.dir(element);
                let listItem = document.createElement("li");
                listItem.innerText = element.event_name;
                listItem.idEvent = element.idEvent;
                getPanneByEvent(element.idEvent, (pannes) => {

                    let lstPanne = document.createElement("ul");
                    pannes.forEach(p => {
                        let listItemPanne = document.createElement("li");
                        listItemPanne.innerText = p.code;
                        let delBtn = document.createElement("button");
                        delBtn.innerText = "X";
                        delBtn.onclick = () => {
                            _alert(`désassocier la panne ${p.id} de l'event ${element.idEvent} ?`);
                        }
                        listItemPanne.appendChild(delBtn);
                        lstPanne.appendChild(listItemPanne);
                    })
                    listItem.appendChild(lstPanne);
                }, (err) => {
                    console.error(err);
                })
                listItem.onclick = function () {
                    panne_select_frm.style.display = "block";
                    let selectName = document.querySelector("#panne_select_frm>h2>span");
                    selectName.idEvent = element.idEvent;
                    selectName.innerText = element.event_name;
                }
                lst.appendChild(listItem);
            });
        }, (err) => {
            _alert(err, 1);
        })
    });
}
async function seeUser(user) {
    loadView("async/view_updateUser", () => {
        let token = JSON.parse(localStorage.getItem("user")).token;
        fetch("async/users_seeUser?user=" + user, {
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        })
            .then(r => { return r.json() })
            .then(result => {
                document.querySelector("#MainActivity #uuidUser").innerText = result.data[0].uuidUser;
                for (var k in result.data[0]) {
                    let sel = `#UpdateUserFrm #${k}`;
                    //  console.log(sel);
                    let compoment = document.querySelector(sel)
                    if (compoment) {
                        if (k == "manager") {
                            try {
                                //Générer le select
                                fetch("async/users_getUsers", {
                                    method: "GET",
                                    headers: {
                                        "Authorization": 'Bearer : ' + token
                                    }
                                })
                                    .then(r => { return r.json() })
                                    .then(resultUser => {
                                        let filtereduser = resultUser.data.filter(u => { return u.roleUser == "manager" });
                                        //   console.dir(filtereduser);
                                        filtereduser.forEach(m => {
                                            console.dir(m);
                                            let opt = document.createElement("option");
                                            opt.value = m.idusers;
                                            opt.innerHTML = m.nomUser + " " + m.prenomUser;
                                            compoment.appendChild(opt);
                                        })
                                        compoment.value = result.data[0]["manager_id"];
                                    })
                                if (!compoment.value) {
                                    console.warn("Manager assigné introuvable parmi les options disponibles");
                                }
                            } catch (err) {
                                console.error("Erreur fetch users_getUsers :", err);
                            }
                        } else
                            compoment.value = result.data[0][k];
                    }
                }
            });
    })
}
async function getUsers() {
    loadView("async/view_getUsers", () => {
        let token = JSON.parse(localStorage.getItem("user")).token;
        fetch("async/users_getUsers", {
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        })
            .then(r => { return r.json() })
            .then(result => {
                let userTable = document.querySelector("#MainActivity table tbody");
                if (result.status === "success") {
                    result.data.forEach(user => {
                        const line = createTableRow([
                            user.nomUser,
                            user.prenomUser,
                            user.mailUser,
                            user.created_at,
                            user.roleUser
                        ]);
                        if (JSON.parse(localStorage.getItem("user")).role == "admin") {
                            let actions = document.createElement("td");
                            let btnPwd = document.createElement("button");
                            btnPwd.classList.add("btn");
                            btnPwd.onclick = function () {
                                refreshPwd(user.uuidUser, () => {
                                    _alert("Le mot de passe à été réinitialisé");
                                }, function (err) {
                                    _alert(`Une erreur s'est produite : ${err}`);
                                })
                            }
                            btnPwd.setAttribute("title", "Reinitialiser le mot de passe");
                            btnPwd.innerHTML = "<i class=\"fa-solid fa-arrows-rotate\"></i>"
                            actions.appendChild(btnPwd);
                            let btnUpdate = document.createElement("button");
                            btnUpdate.classList.add("btn");
                            btnUpdate.onclick = function () {
                                seeUser(user.uuidUser)
                            }
                            btnUpdate.setAttribute("title", "Modifier l'utilsateur");
                            btnUpdate.innerHTML = "<i class=\"fa-solid fa-pen-to-square\"></i>"
                            actions.appendChild(btnUpdate);
                            line.appendChild(actions);
                        }
                        userTable.appendChild(line);
                    })
                } else {
                    if (result.code == "401") {
                        _alert("Problème sur le token de connexion", function () {
                            window.location.href = "deconnexion";
                        }, 1);
                    }
                    if (result.status === "token") {
                        userTable.innerHTML = `token error <b>${result.message}</b>`
                    }
                }
            })
    }, (err) => {
        console.error(err);
    })
}
async function getLog(success, failed) {
    const response = await fetch("async/log_getAll");
    const result = await response.json();
    if (result.status == "success") {
        success?.call(this, result.data);
    } else {
        failed?.call(this, result.message);
    }
}
async function updatePannes(id, newCar, newDiag, success, failed) {
    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "diag": newDiag,
            "cars": newCar
        })
    };
    const response = await fetch(`async/pannes_update&id=` + id, options);
    const result = await response.json();
    if (result.status == "success") {
        success.call(this);
    } else {
        failed.call(this, result.message);
    }
}
async function connexion(mail, mdp, success, failed) {
    const options = {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "mail": mail,
            "secret": mdp
        })
    };
    const response = await fetch(`async/connexion`, options);
    const result = await response.json();
    console.log("here result from fetch");
    console.dir(result);
    if (result.status == "success") {
        success.call(this, result.data);
    } else {
        failed.call(this, result.message);
    }
}
async function associatePanneEvent(eventID, panneID, success, failed) {
    fetch("async/event_associate?event=" + eventID + "&panne=" + panneID)
        .then(r => { return r.json() })
        .then(result => {
            if (result.status == "success") {
                success?.call(this);
            } else {
                failed?.call(this, result.message);
            }
        })
}
async function getPanneByEvent(eventID, success, failed) {
    fetch("async/event_getPannes?event=" + eventID)
        .then(r => { return r.json() })
        .then(result => {
            if (result.status == "success") {
                success?.call(this, result.data);
            } else {
                failed?.call(this, result.message);
            }
        })
}
async function getActionsLicence(success, failed) {
    fetch("include/exception.json")
        .then(r => { return r.json() })
        .then(result => {
            success.call(this, result);
        })
        .catch(error => { failed.call(this, error) });
}
async function createLicence(agent, auto = false, success, failed) {
    fetch("async/licence_add?agent=" + agent + "&auto=" + auto)
        .then(r => { return r.text() })
        .then(result => {
            let r = JSON.parse(result);
            if (r.status == "success") {
                success.call(this, r.data);
            } else {
                failed.call(this, r.message);
            }
        })
        .catch(error => {
            console.error(error);
            failed.call(this, error);
        })
}