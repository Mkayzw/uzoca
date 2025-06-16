CREATE TABLE IF NOT EXISTS agent_subscriptions (
  id SERIAL PRIMARY KEY,
  agent_id int(11) NOT NULL,
  start_date timestamp NOT NULL,
  expiry_date timestamp NOT NULL,
  status varchar(20) NOT NULL DEFAULT 'active',
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT agent_subscriptions_ibfk_1 FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE
);