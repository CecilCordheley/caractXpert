//const { any } = require("async");

let pannesGlobales = {};  // Toutes les pannes
let pannesRestantes = {}; // Celles qui restent après chaque réponse
let questionsPosees = [];
function initSys() {
  pannesGlobales = {};  // Toutes les pannes
  pannesRestantes = {}; // Celles qui restent après chaque réponse
  questionsPosees = [];
}
window.addEventListener("load", function () {
  document.querySelectorAll("[g_area]").forEach(el => {
    el.style["grid-area"] = el.getAttribute("g_area");
  });
  document.getElementById("connexionTrigger")?.addEventListener("click", function () {
    let mail = document.getElementById("Mail_Connexion").value;
    let mdp = document.getElementById("Mdp_Connexion").value;
    connexion(mail, mdp, (data) => {
      console.dir(data);
      localStorage.setItem("user", JSON.stringify(data));
      window.location.href = "MainActivity";
    }, (err) => {
      console.error(err);
    })
  })
});
function setEditableFields() {
  let els = document.querySelectorAll(".editable");
  els.forEach(el => {
    el.onclick = function () {
      let tag = el.tagName;
      let input = document.createElement("input");
      input.type = "text";
      input.value = el.innerText;
      el.replaceWith(input);
      input.onblur = function () {
        let replace = document.createElement(tag);
        replace.classList.add("editable");
        replace.innerText = this.value;
        this.replaceWith(replace);
      }
    }
  });
}
async function getConsole() {
  loadView("async/view_console");
}
async function getMainActivity() {
  let role = JSON.parse(localStorage.getItem("user")).role;
  console.log(role);
  let token = JSON.parse(localStorage.getItem("user")).token;
  switch (role) {
    case "dev":{
      getConsole();
      break;
    }
    case "admin":
      getUsers();
      break;
    case "manager":
      getPannesData();
      break;
  }
}
async function getPannesData() {
  loadView("async/view_pannes", async () => {
    // On récupère les caractéristiques possibles
    let allCaracteristics = [];
    await getAllCaracteristics(
      (data) => {
        // data = [{ id:1, label:"Rouge" }, { id:2, label:"Vert" }, ...]
        allCaracteristics = data;
      },
      (err) => { console.error("Erreur chargement caractéristiques :", err); }
    );

    // On récupère les pannes
    fetch("async/pannes_getAll?ids=1")
      .then(r => r.json())
      .then(result => {
        if (result.status !== "success") {
          console.error("Erreur lors de la récupération des pannes :", result.message);
          return;
        }

        let panneAccordion = document.querySelector("#MainActivity .accordion");
        panneAccordion.innerHTML = '';
        result_ = Object.keys(result.data).map((key) => [{ code: key, data: result.data[key] }]);

        result_.forEach((panne, index) => {
          let panneItem = document.createElement("div");
          panneItem.classList.add("accordion-item");

          // Nouvelle structure : liste d'objets { id, label }
          let currentCaracteristiques = panne[0].data.car.map(carac => ({
            id: carac.id,
            label: carac.label
          }));

          function renderCaracteristiques(container) {
            container.innerHTML = "";
            currentCaracteristiques.forEach(carac => {
              //  console.dir(carac);
              let badge = document.createElement("span");
              badge.className = "badge bg-primary m-1";
              badge.innerText = carac.label;
              badge.idCar = carac.id;
              let closeBtn = document.createElement("button");
              closeBtn.className = "btn-close btn-close-white ms-2";
              closeBtn.style.fontSize = "0.7em";
              closeBtn.style.marginLeft = "5px";
              closeBtn.addEventListener("click", () => {
                currentCaracteristiques = currentCaracteristiques.filter(c => c.id !== carac.id);
                renderCaracteristiques(container);
              });

              badge.appendChild(closeBtn);
              container.appendChild(badge);
            });
          }

          panneItem.innerHTML = `
            <h2 class="accordion-header" id="heading${index}">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse${index}" aria-expanded="false" aria-controls="collapse${index}">
                Code panne : ${panne[0].code}
              </button>
            </h2>
            <div panne_id='${panne[0].data.idPanne}' id="collapse${index}" class="accordion-collapse collapse" aria-labelledby="heading${index}" data-bs-parent="#MainActivity .accordion">
              <div class="accordion-body">
                <label>Diagnostic :</label>
                <p name='diag_name' class='editable'> ${panne[0].data.diagnostique}</p>
                
                <p><strong>Caractéristiques :</strong></p>
                <div car_panne='${panne[0].data.idPanne}' id="caracteristiquesContainer${index}" class="mb-2"></div>
                
                <div class="input-group mb-3">
                  <select id="addCaracteristiqueSelect${index}" class="form-select">
                    <option value="">-- Sélectionner une caractéristique --</option>
                    ${allCaracteristics.map(car => `<option value="${car.id}">${car.label}</option>`).join('')}
                  </select>
                  <button class="btn btn-outline-secondary" type="button" id="addCaracteristiqueButton${index}">Ajouter</button>
                  
                </div>
                <button name='updatePannes' class='btn btn-primary' idPanne='${panne[0].data.idPanne}'>Update</button>
              </div>
            </div>
          `;

          panneAccordion.appendChild(panneItem);

          // Initialisation caractéristiques
          let caracContainer = panneItem.querySelector(`#caracteristiquesContainer${index}`);
          renderCaracteristiques(caracContainer);

          // Gestion bouton ajouter
          panneItem.querySelector(`#addCaracteristiqueButton${index}`).addEventListener("click", () => {
            let select = panneItem.querySelector(`#addCaracteristiqueSelect${index}`);
            let valueId = parseInt(select.value);
            if (!valueId) return;

            let selectedCarac = allCaracteristics.find(c => c.id === valueId);

            if (selectedCarac && !currentCaracteristiques.some(c => c.id === selectedCarac.id)) {
              currentCaracteristiques.push({ id: selectedCarac.id, label: selectedCarac.label });
              renderCaracteristiques(caracContainer);
            }

            select.value = "";
          });
        });

        setEditableFields();
        setUpdatePanne_button();
      })
      .catch(err => {
        console.error("Erreur lors du fetch pannes_getAll :", err);
      });
  }, (err) => {
    console.error("Erreur chargement vue_pannes :", err);
  });
}

function setUpdatePanne_button() {
  let btns = document.querySelectorAll("[name=updatePannes]");
  btns.forEach(btn => {
    btn?.addEventListener("click", function () {
      let id = this.getAttribute("idPanne");
      console.log(id);
      let newDiag = document.querySelector(`[panne_id='${id}'] [name="diag_name"]`).innerText;
      let carEl = Array.from(document.querySelectorAll(`[car_panne='${id}'] span`));
      let newCar = carEl.reduce((c, el) => {
        c.push(el.idCar);
        return c;
      }, []);
      updatePannes(id, newCar, newDiag, () => {
        _alert("panne mise à jour");
      }, (err) => {
        _alert(err, 1);
      })
    })
  })
}
function getFormData(formSelector) {
  const form = document.querySelector(formSelector);
  const formData = new FormData(form);
  const data = {};

  formData.forEach((value, key) => {
    data[key] = value;
  });

  return data;
}
async function getPannes() {
  fetch("async/pannes_getAll?ids=0")
    .then(r => { return r.json() })
    .then(result => {
      if (result.status == "success") {
        pannesGlobales = result.data;
        pannesRestantes = { ...pannesGlobales };
        poserQuestion();
      }
    })
}
function repondre(question, valeur) {
  debugger;
  const nouvellesPannes = {};
  for (const [id, panne] of Object.entries(pannesRestantes)) {
    console.log(panne);
    const aLeSymptome = panne.car.includes(question);
    if (valeur === aLeSymptome) {
      nouvellesPannes[id] = panne;
    }
  }
  pannesRestantes = nouvellesPannes;
  poserQuestion();
}
function afficherResultat() {
  const ids = Object.keys(pannesRestantes);
  const box = document.getElementById("resultBox");

  if (ids.length === 1) {
    const p = pannesRestantes[ids[0]];
    box.innerHTML = `<p><strong>Diagnostic :</strong> ${p.diagnostique}</p>`;
    let validPanne = document.getElementById("validPanne");
      if (validPanne) {
        validPanne.style.display = "block";
      }
  } else if (ids.length > 1) {
    box.innerHTML = `<p>Plusieurs pannes possibles :</p><ul>` +
      ids.map(id => `<li>${pannesRestantes[id].diagnostique}</li>`).join('') + `</ul>`;
  } else {
    box.innerHTML = `<p>Aucune panne ne correspond</p>`;
  }

  document.getElementById("questionBox").innerHTML = '';
}
function poserQuestion() {
  const questions = calculerDiscrimination(pannesRestantes) // Appliquer l'entropie de Shannon
    .filter(q => !questionsPosees.includes(q.question));

  if (questions.length === 0 || Object.keys(pannesRestantes).length <= 1) {
    afficherResultat();
    return;
  }
  //console.dir(questions);
  const question = questions[0].question;;
  questionsPosees.push(question);
  let qBox = document.getElementById("questionBox")
  qBox.innerHTML = `
    <p>Le symptôme est-il présent : <strong>${question}</strong> ?</p>`;
  let yes = document.createElement("button");
  yes.classList.add("btn");
  yes.classList.add("btn-success");
  yes.innerText = "OUI";
  yes.onclick = function () {
    repondre(question, true);
  }
  let no = document.createElement("button");
  no.innerText = "NON"
  no.classList.add("btn");
  no.classList.add("btn-danger");
  no.onclick = function () {
    repondre(question, false);
  }
  qBox.appendChild(yes);
  qBox.appendChild(no);
}
function calculerDiscrimination(pannes) {
  const stats = {};
  const total = Object.keys(pannes).length;

  for (const id in pannes) {

    const caracs = pannes[id].car;
    //   console.log(caracs);
    for (const c of caracs) {
      stats[c] = stats[c] || { avec: 0 };
      stats[c].avec++;
    }
  }

  for (const c in stats) {
    stats[c].sans = total - stats[c].avec;
    stats[c].score = Math.abs(stats[c].avec - stats[c].sans); // Écart à 50/50
  }

  return Object.entries(stats)
    .map(([c, d]) => ({ question: c, score: d.score }))
    .sort((a, b) => a.score - b.score); // Le plus proche de 50/50 en premier
}
