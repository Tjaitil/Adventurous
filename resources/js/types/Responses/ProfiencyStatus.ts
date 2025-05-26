import { AdvApi } from '../../AdvApi';

export interface ProfiencyStatusResponse extends AdvApi {
  html: {
    profiency_status_template: string;
  };
}
