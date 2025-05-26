export const scriptLoader = {
  scripts: [],
  async importBuildingModule(script) {
    return import('/public/js/buildingScripts/' + script);
  },
  loadScript(scriptArray, scriptType) {
    return new Promise((resolve, reject) => {
      let folder;
      if (scriptArray.length === 0) return false;
      switch (scriptType) {
        case 'client':
          folder = '../public/js/clientScripts/';
          break;
        case 'utility':
          folder = '../public/js/utilities/';
          break;
        default:
          break;
      }
      let err = 0;
      scriptArray.forEach(element => {
        const tag = document.createElement('script');
        tag.src = folder + element + '.js';
        tag.type = 'text/javascript';
        document.getElementsByTagName('head')[0].appendChild(tag);
        tag.onerror = () => err++;
      });
      if (err === 0) {
        resolve('Done');
      } else {
        reject('Error loading scripts');
      }
    });
  },
};
