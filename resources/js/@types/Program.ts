export interface Program {
  id: number;
  channel_tvg_slug: string;
  start: string;
  start_tz: string;
  end: string;
  end_tz: string;
  title: string;
  description: string;
  category: string;
  link: string;
  cover_img: string;
  duration: string;
  on_air: boolean;
}
