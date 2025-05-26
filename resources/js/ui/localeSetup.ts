export async function getLanguageBundle() {
  const modules = import.meta.glob('../../../lang/en/*.json');

  let jsonFiles: { [key: string]: { [key: string]: string } | string } = {};

  for (const path in modules) {
    const module = (await modules[path]()) as TranslationObject;
    const fileName = path.split('/').pop();
    const fileNameWithoutExtension = fileName?.split('.')[0] ?? '';
    if (fileNameWithoutExtension === 'index') {
      jsonFiles = { ...jsonFiles, ...module.default };
    } else {
      jsonFiles[fileNameWithoutExtension] = module.default;
    }
  }

  return jsonFiles;
}

interface TranslationObject {
  default: { [key: string]: string };
}
