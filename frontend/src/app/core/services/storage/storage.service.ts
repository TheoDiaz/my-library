import { Injectable } from '@angular/core';
import { Storage } from '@ionic/storage-angular';

@Injectable({
  providedIn: 'root'
})
export class StorageService {
  private _storage: Storage | null = null;

  constructor(private storage: Storage) {
    this.init();
  }

  async init() {
    const storage = await this.storage.create();
    this._storage = storage;
  }

  async set(key: string, value: any): Promise<void> {
    let result = null;
    if (this._storage) {
      result = await this._storage.set(key, value);
    }
    return result;
  }

  async get(key: string): Promise<any> {
    let result = null;
    if (this._storage) {
      result = await this._storage.get(key);
    }
    return result;
  }

  async remove(key: string): Promise<void> {
    let result = null;
    if (this._storage) {
      result = await this._storage.remove(key);
    }
    return result;
  }

  async clear(): Promise<void> {
    let result = null;
    if (this._storage) {
      result = await this._storage.clear();
    }
    return result;
  }
} 