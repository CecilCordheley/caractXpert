<h2>Bienvenue</h2>
<div class="row">
  <div id="sysContent" class="col-4">
    <div id="questionBox"></div>
    <div id="reponseBox"></div>
    <div id="resultBox"></div>
    {var:user.type}
    {:IF {var:user.roleUser}=agent}
    <button id="validPanne" class="btn btn-secondary">Valider</button>
    <button id="triggerEvent" class="btn btn-secondary">Déclencher l'évenement</button>
    {:/IF}
    <script>
      getPannes();
      let validPanne = document.getElementById("validPanne");
      let triggerEvent = document.getElementById("triggerEvent");
      if(triggerEvent!=undefined)
        triggerEvent.style.display = "none";
      if (validPanne) {
        validPanne.style.display = "none";
      }
      document.getElementById("validPanne")?.addEventListener("click", function () {
        let userID = JSON.parse(localStorage.getItem("user")).user_id;
        let panne_id = Object.entries(pannesRestantes)[0][1].idPanne;
        fetch("async/pannes_found?user=" + userID + "&panne=" + panne_id + "&comment=")
          .then(r => { return r.json() })
          .then(result => {
            if (result.status.toLowerCase() == "success") {
              hasEvent(panne_id, (data) => {
                console.dir(data);
                if (data != false)
                  triggerEvent.style.display = "block";
                data.forEach(event => {
                  triggerEvent.addEventListener("click", function () {
                    let panne = Object.entries(pannesRestantes)[0][1];
                    eval(event.event_callBack);
                    this.style.display = "none";
                  })
                });
              }, (err) => {
                console.error(err);
              })
              _alert("Panne Signalée");
              document.getElementById("resultBox").innerHTML = "";
              initSys();
              getPannes();
              let validPanne = document.getElementById("validPanne");
              if (validPanne) {
                validPanne.style.display = "none";
              }
            }
          })
        document.getElementById("resultBox").innerHTML = "";
        initSys();
        getPannes();
      })
    </script>
  </div>
  <div class="col-8" id="MainActivity">
    Ici les composant d'activité
  </div>
  <script>
    getMainActivity();
  </script>
</div>