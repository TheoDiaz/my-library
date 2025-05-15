import { Injectable } from '@angular/core';
import { Preferences } from '@capacitor/preferences';
import { environment } from '../../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class StorageService {
  constructor() {}

  async setToken(token: string): Promise<void> {
    await Preferences.set({
      key: environment.storageKeys.token,
      value: token
    });
  }

  async getToken(): Promise<string | null> {
    const { value } = await Preferences.get({ key: environment.storageKeys.token });
    return value;
  }

  async removeToken(): Promise<void> {
    await Preferences.remove({ key: environment.storageKeys.token });
  }

  async setTheme(theme: 'light' | 'dark'): Promise<void> {
    await Preferences.set({
      key: environment.storageKeys.theme,
      value: theme
    });
  }

  async getTheme(): Promise<'light' | 'dark' | null> {
    const { value } = await Preferences.get({ key: environment.storageKeys.theme });
    return value as 'light' | 'dark' | null;
  }
} 