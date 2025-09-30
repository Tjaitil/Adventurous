export interface Player {
  id: number;
  username: string;
  location: string;
  map_location: string;
  game_id: string;
  session_id: number;
  destination: string;
  arrive_time: string;
  profiency: string;
  horse: string;
  artefact: string;
  hunger: number;
  hunger_date: string;
  frajrite_items: boolean;
  wujkin_items: boolean;
  stockpile_max_amount: number | null;
}
