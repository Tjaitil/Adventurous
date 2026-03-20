export interface Guide {
  title: string;
  html: string;
  slug: string;
  description: string;
  related_guides?: string[];
  tags: string[];
}

export const GUIDE_IDENTIFIER_MAP: Record<
  GuideIdentifier,
  { category: string; slug: string }
> = {
  overview: {
    category: 'general',
    slug: 'overview',
  },
  'skills/farmer': {
    category: 'skills',
    slug: 'farmer',
  },
} as const;

export type GuideIdentifier = 'skills/farmer' | 'overview';
