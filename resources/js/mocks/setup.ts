import { afterAll, beforeAll, afterEach } from 'vitest';
import { setupServer } from 'msw/node';
import { HttpResponse, http } from 'msw';
import { UpdateSkillsResponse } from '@/types/Responses/UpdateSkillsResponse';
import { MockedUpdateSkillsResponse } from './responses/UpdateSkillsResponse';

export const restHandlers = [
  http.post('/skills/update', () => {
    return HttpResponse.json<UpdateSkillsResponse>(MockedUpdateSkillsResponse);
  }),
];

const server = setupServer(...restHandlers);

beforeAll(() => { server.listen({ onUnhandledRequest: 'error' }); });

afterAll(() => { server.close(); });

afterEach(() => { server.resetHandlers(); });
