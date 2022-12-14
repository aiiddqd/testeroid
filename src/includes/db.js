import { openDB } from 'idb';

const dbPromise = openDB('mmd', 1, {
    upgrade(db) {
        db.createObjectStore('notes');
    },
});

export async function set(key, val) {
    return (await dbPromise).put('notes', val, key);
}

export async function get(key) {
    return (await dbPromise).get('notes', key);
}
