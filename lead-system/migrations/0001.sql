PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS leads (
  id TEXT PRIMARY KEY,
  submission_id TEXT NOT NULL UNIQUE,
  submission_hash TEXT NOT NULL,
  created_at TEXT NOT NULL,
  updated_at TEXT NOT NULL,
  version INTEGER NOT NULL DEFAULT 1,
  change_id TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'new' CHECK (status IN ('new','contacted','quoted','won','lost')),
  servizio TEXT NOT NULL,
  zona TEXT NOT NULL,
  quantita TEXT NOT NULL DEFAULT '',
  periodo TEXT NOT NULL DEFAULT '',
  dettagli TEXT NOT NULL DEFAULT '',
  nome TEXT NOT NULL,
  azienda TEXT NOT NULL DEFAULT '',
  email TEXT NOT NULL,
  telefono TEXT NOT NULL DEFAULT '',
  privacy_accepted_at TEXT NOT NULL,
  privacy_policy_version TEXT NOT NULL,
  marketing_consent INTEGER NOT NULL DEFAULT 0 CHECK (marketing_consent IN (0,1)),
  google_ads_consent INTEGER NOT NULL DEFAULT 0 CHECK (google_ads_consent IN (0,1)),
  meta_ads_consent INTEGER NOT NULL DEFAULT 0 CHECK (meta_ads_consent IN (0,1)),
  source TEXT NOT NULL CHECK (source IN ('manual','wordpress','test')),
  notification_status TEXT NOT NULL DEFAULT 'not_configured',
  is_test INTEGER NOT NULL DEFAULT 0 CHECK (is_test IN (0,1))
);
CREATE INDEX IF NOT EXISTS leads_created ON leads(created_at DESC, id DESC);
CREATE INDEX IF NOT EXISTS leads_status_created ON leads(status, created_at DESC);

CREATE TABLE IF NOT EXISTS lead_activity (
  id TEXT PRIMARY KEY,
  lead_id TEXT NOT NULL REFERENCES leads(id) ON DELETE CASCADE,
  created_at TEXT NOT NULL,
  actor TEXT NOT NULL,
  action TEXT NOT NULL CHECK (action IN ('created','status_changed','note_added')),
  previous_status TEXT,
  new_status TEXT,
  note TEXT NOT NULL DEFAULT ''
);
CREATE INDEX IF NOT EXISTS activity_lead ON lead_activity(lead_id, created_at DESC);
