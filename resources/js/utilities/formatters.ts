import { jsUcWords } from './uppercase';

export function formatLocationName(location: string): string {
  return jsUcWords(location.replace(/-/g, ' '));
}

export function formatCharacterName(name: string): string {
  return jsUcWords(name.replace(/-/g, ' '));
}
