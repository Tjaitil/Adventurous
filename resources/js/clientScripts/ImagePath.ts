export class AssetPaths {
  private static imagePath = '/images/';

  static getImagePath(src: string): string {
    return this.imagePath + src;
  }

  /**
   * Get the path to a PNG image
   */
  static getImagePngPath(src: string): string {
    return this.getImagePath(src + '.png');
  }
}
