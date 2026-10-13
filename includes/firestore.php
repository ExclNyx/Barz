<?php
use Google\Cloud\Firestore\FirestoreClient;

/** Return the official Google Cloud Firestore PHP client. */
function firestoreClient(): FirestoreClient {
    static $client = null;
    if ($client instanceof FirestoreClient) return $client;

    if (!class_exists(FirestoreClient::class)) {
        throw new RuntimeException('Library Firestore belum terpasang. Jalankan: composer require google/cloud-firestore');
    }

    $credentials = getenv('GOOGLE_APPLICATION_CREDENTIALS');
    if (!$credentials || !is_file($credentials)) {
        throw new RuntimeException('Kredensial Firebase belum diatur. Set GOOGLE_APPLICATION_CREDENTIALS ke file service-account JSON yang disimpan di luar folder aplikasi.');
    }

    $client = new FirestoreClient(['projectId' => FIREBASE_PROJECT_ID]);
    return $client;
}

/** Convert a Firestore document snapshot into a plain array with a compatible id field. */
function fs_snapshot_data($snapshot): ?array {
    if (!$snapshot || !$snapshot->exists()) return null;
    $data = $snapshot->data();
    $id = $snapshot->id();
    $data['id'] = ctype_digit((string)$id) ? (int)$id : (string)$id;
    return $data;
}

/** Read one document by its ID. */
function fs_get(string $collection, $id): ?array {
    $snapshot = firestoreClient()->collection($collection)->document((string)$id)->snapshot();
    return fs_snapshot_data($snapshot);
}

/** Return all documents in a collection. Appropriate for this small demo; use server-side queries at scale. */
function fs_all(string $collection): array {
    $documents = [];
    foreach (firestoreClient()->collection($collection)->documents() as $snapshot) {
        $data = fs_snapshot_data($snapshot);
        if ($data !== null) $documents[] = $data;
    }
    return $documents;
}

/** Filter a small collection in PHP. */
function fs_filter(string $collection, callable $predicate): array {
    return array_values(array_filter(fs_all($collection), $predicate));
}

function fs_find(string $collection, callable $predicate): ?array {
    foreach (fs_all($collection) as $row) {
        if ($predicate($row)) return $row;
    }
    return null;
}

/** Insert a document; numeric IDs keep existing form values and foreign-key fields compatible. */
function fs_add(string $collection, array $data, $id = null) {
    $db = firestoreClient();
    if ($id === null) {
        do { $id = random_int(100000, 999999999); }
        while ($db->collection($collection)->document((string)$id)->snapshot()->exists());
    }
    $data['id'] = ctype_digit((string)$id) ? (int)$id : (string)$id;
    $now = date('c');
    $data['created_at'] = $data['created_at'] ?? $now;
    $data['updated_at'] = $data['updated_at'] ?? $now;
    $db->collection($collection)->document((string)$id)->set($data);
    return ctype_digit((string)$id) ? (int)$id : (string)$id;
}

/** Upsert a document with a known ID, useful for settings and seed data. */
function fs_put(string $collection, $id, array $data): void {
    $ref = firestoreClient()->collection($collection)->document((string)$id);
    $existing = fs_snapshot_data($ref->snapshot());
    $data['id'] = ctype_digit((string)$id) ? (int)$id : (string)$id;
    $data['created_at'] = $data['created_at'] ?? ($existing['created_at'] ?? date('c'));
    $data['updated_at'] = date('c');
    $ref->set($data);
}

/** Update fields while retaining fields not included in the update. */
function fs_update(string $collection, $id, array $changes): bool {
    $current = fs_get($collection, $id);
    if ($current === null) return false;
    unset($changes['id'], $changes['created_at']);
    $merged = array_replace($current, $changes);
    $merged['updated_at'] = date('c');
    firestoreClient()->collection($collection)->document((string)$id)->set($merged);
    return true;
}

function fs_delete(string $collection, $id): bool {
    $ref = firestoreClient()->collection($collection)->document((string)$id);
    if (!$ref->snapshot()->exists()) return false;
    $ref->delete();
    return true;
}

function fs_compare_id($a, $b): bool {
    return (string)$a === (string)$b;
}

function fs_now(): string { return date('c'); }
