export class AssetPaths {
    private static imagePath = '/images/';

    static getImagePath(src: string): string {
        return this.imagePath + src;
    }
}
