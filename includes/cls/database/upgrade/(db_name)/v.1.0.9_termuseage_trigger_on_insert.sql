-- multi_query
CREATE TRIGGER `term_count_insert` AFTER INSERT ON `termusages` FOR EACH ROW
BEGIN
	IF(NEW.termusage_foreign = 'posts') THEN
		BEGIN
			UPDATE terms SET terms.term_count = IF(terms.term_count IS NULL, 1, terms.term_count + 1) WHERE	terms.id = NEW.term_id;
		END;
	END IF;

	IF(NEW.termusage_foreign = 'users') THEN
		BEGIN
			UPDATE terms SET terms.term_usercount = IF(terms.term_usercount IS NULL, 1, terms.term_usercount + 1) WHERE	terms.id = NEW.term_id;
		END;
	END IF;
END