export type ApiRequestData = {
  model: string;
  method: string;
  use_response?: boolean;
  [key: string]: any;
};
