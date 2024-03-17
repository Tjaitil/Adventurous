import { LocalMapTags } from './../types/LocalMapTags';
import { MapFontProperties, MapPlayerMarker } from './../types/Map';
import { BaseMapTag } from './../types/BaseMapTag';
import { jsUcfirst } from '../utilities/uppercase';
import { Game } from '../advclient';
import { GamePieces } from './gamePieces';

window.addEventListener('load', () => {
    document
        .getElementById('toggle_world_image')
        .addEventListener('click', () => Map.toggleMapType());
    document
        .getElementById('map_type_toggle_overlay')
        .addEventListener('click', () => Map.toggleMapType());
    document
        .getElementById('toggle_icon_list_image')
        .addEventListener('click', () => Map.toggleIconList());
    // Add events to both toggle map button on canvas and the close button in map container
    document
        .getElementById('toggle_map_icon')
        .addEventListener('click', () => Map.toggle());
    document
        .getElementById('close_map_button')
        .addEventListener('click', Map.toggle);
});

export class Map {
    private static worldImgContainer = document.getElementById(
        'map_world_img_container',
    );
    private static localIMGElement = <HTMLImageElement>(
        document.getElementById('local_img')
    );
    private static mapIconListElement = <HTMLElement>(
        document.getElementById('map_icon_list')
    );
    private static fontProperties: MapFontProperties = {
        city: 32,
        placeName: 24,
    };
    private static iconListVisible: boolean = false;
    private static playerMarker: MapPlayerMarker = {
        tagType: 'dot',
        visible: false,
        x: 0,
        y: 0,
    };
    private static localMapTags: LocalMapTags[] = [
        {
            id: 1,
            tagType: 'img',
            tagIdentifier: 'pesr',
            mapParent: '',
            src: 'travel icon',
            type: 'icon',
            visible: false,
            x: 0,
            y: 0,
        },
        {
            id: 2,
            tagType: 'img',
            tagIdentifier: 'sailor',
            mapParent: '',
            src: 'boat travel icon',
            type: 'icon',
            visible: false,
            x: 0,
            y: 0,
        },
    ];

    private static mapTag: BaseMapTag[] = [
        {
            id: 1,
            x: 50,
            y: 30,
            text: 'Towhar',
            type: 'city',
            mapParent: '5.7',
            tagType: 'text',
        },
        {
            id: 2,
            x: 50,
            y: 50,
            text: 'Golbak',
            type: 'city',
            mapParent: '3.5',
            tagType: 'text',
        },
        {
            id: 3,
            x: 50,
            y: 20,
            text: 'Fansal Plains',
            type: 'city',
            mapParent: '4.3',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 50,
            text: 'Snerpiir',
            type: 'city',
            mapParent: '5.5',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 50,
            text: 'Ter',
            type: 'city',
            mapParent: '6.3',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 50,
            text: 'Khanz',
            type: 'city',
            mapParent: '8.2',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            text: 'Hirtam',
            type: 'city',
            mapParent: '4.9',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            text: 'Pvitul',
            type: 'city',
            mapParent: '2.9',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            text: 'Cruendo',
            type: 'city',
            mapParent: '6.6',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            text: 'Fagna',
            type: 'city',
            mapParent: '7.5',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            text: 'Krasnur',
            type: 'city',
            mapParent: '3.6',
            tagType: 'text',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            text: 'Tasnobil',
            type: 'city',
            mapParent: '2.6',
            tagType: 'text',
        },
        {
            id: 4,
            x: 100,
            y: 90,
            text: 'Wilsna´s point',
            type: 'placeName',
            mapParent: '8.4',
            tagType: 'text',
        },
        {
            id: 4,
            x: 150,
            y: 100,
            text: 'Heskils mountains',
            type: 'placeName',
            mapParent: '4.4',
            tagType: 'text',
        },
        {
            id: 4,
            x: 100,
            y: 90,
            text: 'Tibs pass',
            type: 'placeName',
            mapParent: '7.3',
            tagType: 'text',
        },
        {
            id: 4,
            x: 120,
            y: 140,
            src: 'combat icon',
            type: 'icon',
            mapParent: '4.2',
            tagType: 'img',
        },
        {
            id: 4,
            x: 50,
            y: 40,
            src: 'combat icon',
            type: 'icon',
            mapParent: '3.10',
            tagType: 'img',
        },
        {
            id: 4,
            x: 50,
            y: 80,
            src: 'combat icon',
            type: 'icon',
            mapParent: '8.3',
            tagType: 'img',
        },
        {
            id: 4,
            x: 50,
            y: 80,
            src: 'combat icon',
            type: 'icon',
            mapParent: '6.2',
            tagType: 'img',
        },
    ];
    private static mapType: string = 'local';

    public static load(currentMap) {
        this.localIMGElement.src = 'images/' + currentMap + 'm.png';
        this.drawTags();
        this.checkImages();
        this.loadLocalMapTags();
        this.locatePlayerMarker();
    }

    private static loadLocalMapTags() {
        this.localMapTags.forEach(element => {
            const pos = this.findLocalMapTags(
                element.tagIdentifier,
                'character',
            );
            if (pos === false) {
                element.visible = true;
                element.x = pos[0];
                element.y = pos[1];
            } else {
                element.visible = false;
                element.x = 0;
                element.y = 0;
            }
        });
        this.drawLocalTags();
    }

    private static locatePlayerMarker() {
        const playerMarker = document.getElementById('map_player_marker');
        let playerX = GamePieces.player.xpos;
        let playerY = GamePieces.player.ypos;
        if (this.mapType === 'local') {
            if (playerMarker.parentElement.id !== 'map_local_img_container') {
                document
                    .getElementById('map_world_img_container')
                    .removeChild(playerMarker);
                document
                    .getElementById('map_local_img_container')
                    .appendChild(playerMarker);
            } else if (!playerMarker.parentElement.id) {
                document
                    .getElementById('map_local_img_container')
                    .appendChild(playerMarker);
            }
            playerX /= 2;
            playerY /= 2;
        } else if (this.mapType === 'world') {
            if (playerMarker.parentElement.id !== 'map_world_img_container') {
                // document.getElementById("map_local_img_container").removeChild(playerMarker);
                document
                    .getElementById('map_world_img_container')
                    .appendChild(playerMarker);
            }
            const map = Game.properties.currentMap.split('.');
            playerX = playerX / 16 + (parseInt(map[0]) - 1) * 200 - 12.5;
            playerY = playerY / 16 + (parseInt(map[1]) - 1) * 200 - 12.5;
        }
        playerMarker.style.top = playerY + 'px';
        playerMarker.style.left = playerX + 'px';
    }

    private static findLocalMapTags(
        variable: string,
        type: string,
    ): Object | boolean {
        if (type === 'character') {
            const objects = GamePieces.characters.filter(
                object =>
                    object.type === 'character' &&
                    object.src.indexOf(variable) !== -1,
            );
            if (objects.length > 0) {
                const object = objects[0];
                return { x: object.x, y: object.y };
            }
        } else {
            return false;
        }
    }

    private static checkImages() {
        const images = document.getElementsByClassName('world_img');

        for (const img of images) {
            const image = <HTMLImageElement>img;

            // Load default water image on failed load
            if (image.naturalHeight === 0) {
                image.src = 'images/1.10m.png';
            }
        }
    }

    private static drawTags() {
        for (const i of this.mapTag) {
            const mapParentNumbers = i.mapParent.split('.');
            let element;
            if (i.tagType === 'text') {
                const tag = document.createElement('span');
                tag.classList.add('absolute', 'text-white');
                tag.style.fontSize = this.fontProperties[i.type] + 'px';
                tag.innerText = i.text;
                element = tag;
            } else {
                const tag = document.createElement('img');
                tag.src = 'images/' + i.src + '.png';
                element = tag;
            }
            element.classList.add('absolute', 'mapTag');
            element.style.left =
                i.x + (parseInt(mapParentNumbers[0]) - 1) * 200 + 'px';
            element.style.top =
                i.y + (parseInt(mapParentNumbers[1]) - 1) * 200 + 'px';
            this.worldImgContainer.appendChild(element);
        }
    }

    private static drawLocalTags() {
        const tags = document
            .getElementById('map_local_img_container')
            .querySelectorAll('.localTag');
        if (tags.length > 0) {
            for (let i = 0; i < tags.length; i++) {
                document
                    .getElementById('map_local_img_container')
                    .removeChild(tags[i]);
            }
        }
        for (const i of this.localMapTags) {
            if (i.visible === false) {
                continue;
            }
            const tag = document.createElement('img');
            tag.src = 'images/' + i.src + '.png';
            tag.classList.add('mapTag', 'w-[32]', 'h-[32]');
            // Divide by 2 because the image is styled 1600px by 1600px. map.css -> #local_img
            tag.style.left = i.x / 2 + 'px';
            tag.style.top = i.y / 2 + 'px';
            document.getElementById('map_local_img_container').appendChild(tag);
        }
    }

    public static toggle() {
        const mapContainer = document.getElementById('map_container');
        if (mapContainer.style.visibility != 'visible') {
            mapContainer.style.visibility = 'visible';
            mapContainer.style.left = '0px';
        } else {
            document.getElementById('map_icon_list').style.visibility =
                'hidden';
            mapContainer.style.visibility = 'hidden';
            mapContainer.style.left = mapContainer.offsetWidth + 'px';
        }
    }

    public static toggleIconList() {
        // Show sidebar
        if (this.iconListVisible) {
            this.mapIconListElement.style.width = '0%';
            setTimeout(
                () => (this.mapIconListElement.style.visibility = 'hidden'),
                200,
            );
        } else {
            this.mapIconListElement.style.visibility = 'visible';
            this.mapIconListElement.style.width = '30%';
        }
        this.iconListVisible = !this.iconListVisible;
    }

    public static toggleMapType() {
        if (this.mapType === 'local') {
            document.getElementById('map_local_img_container').style.display =
                'none';
            document.getElementById('map_world_img_container').style.display =
                'grid';
            document.getElementById(
                'map_type_toggle_overlay',
            ).style.visibility = 'visible';
            this.mapType = 'world';
            document
                .getElementById('map_container_header')
                .querySelectorAll('h2')[0].innerText =
                jsUcfirst(this.mapType) + ' map';
        } else {
            document.getElementById('map_local_img_container').style.display =
                'block';
            document.getElementById('map_world_img_container').style.display =
                'none';
            document.getElementById(
                'map_type_toggle_overlay',
            ).style.visibility = 'hidden';
            this.mapType = 'local';
            document.getElementById('map_header').innerText =
                jsUcfirst(this.mapType) + ' map';
        }
        this.locatePlayerMarker();
    }
}
