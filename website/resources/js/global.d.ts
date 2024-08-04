import {MapData} from "./index";

declare global {
    interface Window {
        mapData: MapData
    }
}
