import type { GameLog } from '@/types/GameLog';

export const parseGameLog = (log: GameLog['message']): string | string[] => {
  const message = log;
  if (message.includes('{gold}')) {
    // Split on {gold} while keeping the delimiters in the result
    return message.split(/(\{gold\})/).filter(part => part !== '');
  }

  return [message];
};
