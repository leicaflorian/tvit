import { Program } from './Program'

export interface Channel {
  id: number;
  name: string;
  tvg_slug: string;
  tvg_name: string;
  tvg_code: string;
  iptv_code: string;
  logo_url_color: string;
  logo_url_light: string;
  dtt_num: number;
  group: string;
  m3u8_ink: string;
  raw_guide_link: string;
  now_on_air: Program | null;
  next_on_air: Program | null;
  programs: Program[];
}
