class AsyncCall{
    constructor(){
        this.storage=[];
    }
    addQuery(name,fnc){
        this.storage[name]=fnc;
    }
}