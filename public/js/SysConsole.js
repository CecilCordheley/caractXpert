class SysConsole {
    static cmds = {};

    constructor(inputElement, resultContainer) {
        this.input = inputElement;
        this.index = 0;
        this.input.addEventListener("keyup", (e) => {
            if (e.keyCode == 38) { // UP
                if (this.index > 0) {
                    this.index--;
                    this.input.value = this.historic[this.index];
                }
            } else if (e.keyCode == 40) { // DOWN
                if (this.index < this.historic.length - 1) {
                    this.index++;
                    this.input.value = this.historic[this.index];
                } else {
                    this.index = this.historic.length;
                    this.input.value = "";
                }
            } else if (e.keyCode == 13) { // ENTER
                this.runCommand();
                this.index = this.historic.length; // reset index
            }
        });

        this.result = resultContainer;
        this.historic = [];
    }

    static addCmd(name, fnc) {
        this.cmds[name] = fnc;
    }

    static parseCommand(str) {
        const regex = /(.*?)\((.*?)\)/;
        const m = regex.exec(str);
        if (m) {
            return {
                name: m[1].trim(),
                args: m[2] ? m[2].split(',').map(arg => arg.trim()) : []
            };
        }
        return null;
    }

    async runCommand() {
        let str = this.input.value;
        this.historic.push(str);
        const parsed = SysConsole.parseCommand(str);
        if (!parsed) {
            this._displayError(`Invalid command: ${str}`);
            return;
        }

        const { name, args } = parsed;

        if (SysConsole.cmds[name]) {
            try {
                const result = await SysConsole.cmds[name](args, this);
            } catch (e) {
                this._displayError(`Error: ${e.message}`);
            }
        } else {
            this._runRemote(name, args);
        }
    }

    _runRemote(name, args) {
        const query = args.map((arg, index) => `arg${index}=${encodeURIComponent(arg)}`).join('&');
let token = JSON.parse(localStorage.getItem("user")).token;
        fetch(`async/MyCmd_${name}?${query}`,{
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        })
            .then(r => r.json())
            .then(result => {
                if (result.status === "success") {
                    if (result.type == "array") {
                        let table = document.createElement("table");
                        table.classList.add("table");
                        for (var el in result.result) {
                            var row = table.insertRow(0);
                            let cell = row.insertCell();
                            cell.innerText = result.result[el];
                        }
                        this.result.appendChild(table);
                    } else if (result.type == "list") {
                        let list = document.createElement("ul");
                        result.result.forEach(el => {
                            let item = document.createElement("li");
                            item.innerText = el;
                            list.appendChild(item);
                        });
                        this.result.appendChild(list);
                    } else {
                        this._displayResult(result.result);
                    }
                } else {
                    this._displayError(result.message);
                }
            })
            .catch(err => this._displayError(`Fetch error: ${err.message}`));
    }

    _displayResult(msg) {
        this.result.innerHTML += `<div class='result'>${msg}</div>`;
    }

    _displayError(msg) {
        this.result.innerHTML += `<div class='error'>${msg}</div>`;
        console.error(msg);
    }
}

// Exemple d'ajout de commande
SysConsole.addCmd("seeAgent", async function (args, sys) {
    
    return fetch("async/MyCmd_seeAgent")
        .then(r => { return r.json() })
        .then(result => {
            if (result.status === "success") {
                let html = '<table class=\'table\'>';
                result.result.forEach(element => {
                    let line = "<tr>";
                    for (var field in element)
                        line += `<td>${element[field]}</td>`;
                    line += "</tr>";
                    html += line;
                });
                html += "</table>";
                sys._displayResult(`${html}<br>nbAgent : ${result.result.length}`);
            } else {
                sys._displayError(result.message);
            }
        })
})
SysConsole.addCmd("resetPwd",async function(args,sys){

});
SysConsole.addCmd("writeFile", async function (args, sys) {
let token = JSON.parse(localStorage.getItem("user")).token;
    return fetch("async/MyCmd_writeFile?file=" + args[0].replaceAll('\'', '') + "&message=" + encodeURIComponent(args[1]),{
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        })
        .then(r => { return r.json() })
        .then(result => {
            if (result.status == "success") {
                sys._displayResult(`Fichier ${args[0]} écrit avec : ${args[1]}`);
            } else {
                sys._displayError(result.message);
            }
        })
});
SysConsole.addCmd("calc", function (args, sys) {
    const safeExpr = args[0].replace(/[^-()\d/*+.]/g, '');
    let result = eval(safeExpr);
    sys._displayResult(`${args[0]} = ${result}`);
});

SysConsole.addCmd("clearConsole", function (args, sys) {
    sys.result.innerHTML = "";
})
SysConsole.addCmd("archiveToken", async function (args, sys) {
    let token = JSON.parse(localStorage.getItem("user")).token;
    return fetch("async/MyCmd_archiveToken?file=" + args[0].replaceAll('\'', ''),{
            method: "GET",
            headers: {
                "Authorization": 'Bearer : ' + token
            }
        })
        .then(r => { return r.json() })
        .then(result => {
            if (result.status == "success") {
                sys._displayResult(`tokens save to ${args[0]}`);
            } else {
                sys._displayError(result.message);
            }
        })
});
