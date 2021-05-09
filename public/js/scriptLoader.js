const scriptLoader = {
    scripts: [],
    loadScript(scriptArray) {
        if(scriptArray.length === 0) return false;
        scriptArray.forEach(element => {
            let tag = document.createElement("script");
            tag.src = './public/js/clientScripts/' + element + ".js";
            tag.type = "text/javascript";
            document.getElementsByTagName("section")[0].appendChild(tag);
            this.scripts.push[{name: element, loading: true}];
            tag.onload = () => {
                this.scripts.map((script) => {
                    if(script.name === "name") {
                        return {...script, loading: false};
                    }
                })
            };
            tag.onerror = () =>  {alert("Script couldn't load")};
        });
        this.checkLoadedScripts();
    },
    checkLoadedScripts() {
        let number = this.scripts.filter((script) => {
            return (script.loading === true)
        });
        if(number.length == 0) {
            console.log('finished loading');
            this.scripts = [];
        }
    }
};