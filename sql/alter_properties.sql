ALTER TABLE properties
ADD COLUMN agent_id integer NOT NULL,
ADD CONSTRAINT properties_ibfk_1 FOREIGN KEY (agent_id) REFERENCES users (id) ON DELETE CASCADE;