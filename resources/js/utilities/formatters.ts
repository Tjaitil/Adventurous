import { jsUcWords } from './uppercase';

export function formatLocationName(location: string): string {
  return jsUcWords(location.replace(/-/g, ' '));
}

export function formatCharacterName(name: string): string {
  return jsUcWords(name.replace(/-/g, ' '));
}

export function formatItemAmount(amount: number): string | number {
  if (amount > 1000) {
    return `${(amount / 1000).toFixed(1)} k`;
  }

  return amount;
}
