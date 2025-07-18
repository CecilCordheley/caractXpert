HTMLCanvasElement.prototype.clearCanvas=function(){
    this.clearRect(0,0,this.width,this.height);
}
async function getTicketByServiceWithStat(containerID){
  fetch('async/StatFnc_getTicketByServiceWithSat')
  .then(res => res.json())
  .then(response => {
    const rawData = response.data;

    const labels = [...new Set(rawData.map(d => d.label))];
    const etats = [...new Set(rawData.map(d => d.etat))];

    const getColorForEtat = etat => {
      const colors = {
        default: "#dfe6e9",
        valider: "#55efc4",
        echouer: "#d63031",
        assigner: "#74b9ff",
        traiter: "#F00"
      };
      return colors[etat] || "#636e72";
    };

    const datasets = etats.map(etat => ({
      label: etat,
      data: labels.map(label => {
        const match = rawData.find(d => d.label === label && d.etat === etat);
        return match ? match.total : 0;
      }),
      backgroundColor: getColorForEtat(etat),
      stack: "stack1"
    }));
    container=document.getElementById(containerID)
    container.innerHTML="";
const canvas = document.createElement("canvas");
    container.appendChild(canvas);
    const ctx = canvas.getContext("2d");
    new Chart(ctx, {
      type: "bar",
      data: {
        labels,
        datasets
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: "Tickets par service et par état"
          }
        },
        scales: {
          x: { stacked: true },
          y: { stacked: true }
        }
      }
    });
  });

}
async function renderTicketChart({ url, containerId, title = "Tickets", chartType = "bar" }) {
  try {
    const res = await fetch(url);
    const data = await res.json();

    const labels = data.data.map(d => d.label);
    const values = data.data.map(d => d.total);

    const container = document.getElementById(containerId);
    if (!container) throw new Error("Container not found: " + containerId);

    container.innerHTML = "";
    const canvas = document.createElement("canvas");
    container.appendChild(canvas);
    const ctx = canvas.getContext("2d");

    new Chart(ctx, {
      type: chartType,
      data: {
        labels,
        datasets: [{
          label: 'Nombre de tickets',
          data: values,
          backgroundColor: '#4e79a7',
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: title
          },
          legend: { display: false }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });

  } catch (err) {
    console.error("Erreur lors du rendu du graphique :", err);
  }
}
