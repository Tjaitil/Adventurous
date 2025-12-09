export function jsUcfirst(string: string): string {
  // Return uppercase first letter
  return string.charAt(0).toUpperCase() + string.slice(1);
}
export function jsUcWords(string: string): string {
  // Return uppercase first letter each word
  return string.replace(/\w\S*/g, function (txt) {
    return txt.charAt(0).toUpperCase() + txt.slice(1).toLowerCase();
  });
}
