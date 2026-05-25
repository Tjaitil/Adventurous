import { BaseStaticGameObject } from './BaseStaticGameObject';
import type { ICharacterGameObject } from '../types/gamepieces/GameObject';
import { formatCharacterName } from '@/utilities/formatters';

export class Character
  extends BaseStaticGameObject
  implements ICharacterGameObject
{
  type: 'character';
  width: number = 42;
  height: number = 42;
  hasConversation: boolean;
  displayName: string;

  constructor(initCharacterData: ICharacterGameObject) {
    super(initCharacterData);
    this.y += 6;

    this.type = 'character';
    this.diameterUp += 6;
    this.hasConversation = initCharacterData.hasConversation;
    this.displayName = formatCharacterName(initCharacterData.displayName);
  }
}
