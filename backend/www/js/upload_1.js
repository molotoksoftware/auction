var Utils;
if(Utils==undefined)
    Utils={};
        
if(Utils.Uploader==undefined)
    Utils.Uploader={};
        
Utils.Uploader.Core=function(){
    var that=this;
    var params={
        maxFileSize:(1000*1000*4),
            url:null,
        fileInput:null,
        onImageDropEvent:null,
        dropZone:null,
        dropNotify:null
    };
    
    var connection=function(paramsObj){
        var that=this;
        var onParamSuccess=paramsObj.OnSuccess;
        var onParamError=paramsObj.OnError;
        var onParamGlobalError=paramsObj.OnGlobalError;
        var readResponse=function(xhr){
            var response=xhr.responseText;
            if(response.indexOf('<')>-1){
                return{
                    ErrorText:'абаОаИаЗаОбаЛаА аОбаИаБаКаА'
                };
            
        }
        if(xhr.getResponseHeader('Content-Type')!='application/json')
            response=$.parseJSON(response);
        return response;
    };
    
    that.SendFileWithAjaxForm=function(){
        var form=params.fileInput.closest('form');
        form.ajaxSubmit({
            url:params.url,
            type:'POST',
            success:function(response,status,xhr){
                response=readResponse(xhr);
                if(response.Status=='Success'){
                    response.Files[0].Index=0;
                    onParamSuccess(response.Files);
                }else
                    onParamGlobalError({
                        errorText:response.ErrorText,
                        indexOffset:0,
                        fileCountError:1
                    });
            },
            error:function(){
                onParamGlobalError({
                    errorText:'абаОаИаЗаОбаЛаА аОбаИаБаКаА',
                    indexOffset:0,
                    fileCountError:1
                });
            }
        });
};

that.SendFilesWithFileApi=function(files,fileCount){
    var filesPack=function(filesOffset){
        var that=this;
        var form=new FormData();
        var size=0;
        var packedFileCount=0;
        var indexes=[];
        var onComplete=function(response){
            if(response.Status=='Success'){
                var i=0;
                while(response.Files[i]){
                    response.Files[i].Index=indexes[i];
                    i++;
                }
                onParamSuccess(response.Files);
            }else
                onParamGlobalError(response.ErrorText,filesOffset);
        };
        
        var onError=function(message){
            onParamGlobalError({
                errorText:message,
                indexOffset:filesOffset,
                fileCountError:packedFileCount
            });
        };
        
        var postFormData=function(formData,url){
            var xhr=new XMLHttpRequest();
            xhr.onload=function(){
                if(xhr.status==200&&xhr.readyState==4){
                    var response=readResponse(xhr);
                    onComplete(response);
                }else if(xhr.status==500&&xhr.readyState==4){
                    onError('абаОаИаЗаОбаЛаА аОбаИаБаКаА');
                }
            };
            
        xhr.open("post",url,true);
        xhr.send(formData);
    };
    
    that.Add=function(file,index){
        form.append("file"+index,file);
        size=size+file.size;
        packedFileCount++;
        indexes.push(index);
    };
    
    that.CanAdd=function(file){
        return size+file.size<params.maxFileSize;
    };
    
    that.Send=function(){
        postFormData(form,params.url);
    };
    
    that.IsEmpty=function(){
        return packedFileCount===0;
    };

};

var pack=new filesPack(0);
for(var fileIndex=0;fileIndex<fileCount;fileIndex++){
    var curFile=files[fileIndex];
    if(curFile.size>params.maxFileSize){
        onParamError({
            ErrorText:'аЄаАаЙаЛ баЛаИбаКаОаМ аБаОаЛббаОаЙ',
            Name:curFile.name,
            Index:fileIndex
        });
        continue;
    }
    if(!pack.CanAdd(curFile)){
        pack.Send();
        pack=new filesPack(fileIndex);
    }
    pack.Add(curFile,fileIndex);
}
if(!pack.IsEmpty())
    pack.Send();
return true;
};

};

var dragdrop=function(){
    var that=this;
    var isDropDisable=false;
    var onImageDropEvent=null;
    var onDrop=function(event){
        if(event)
            event.preventDefault?event.preventDefault():(event.returnValue=false);else
            return false;
        if(!isDropDisable&&onImageDropEvent)onImageDropEvent(event.dataTransfer.files);
        isDropDisable=false;
        return true;
    };
    
    that.Init=function(onImageDrop,dropZone,dropNotify){
        if(typeof(dropZone)=="undefined"||!dropZone||typeof(onImageDrop)!="function")return false;
        onImageDropEvent=onImageDrop;
        var index=dropZone.length;
        var curDropZone;
        while(index--){
            curDropZone=dropZone[index];
            curDropZone.ondrop=function(event){
                onDrop(event);
            };
            
            curDropZone.addEventListener('dragstart',function(event){
                isDropDisable=true;
            });
        }
        if(typeof(dropNotify)!="undefined"&&dropNotify)dropNotify.show();
        return true;
    };

};

var isBrowserSupportFileApi=typeof(window.FileReader)!='undefined';
var isBrowserSupportFormData=typeof(window.FormData)!='undefined';
if(('ontouchstart'in document.documentElement)||window.Touch){
    document.documentElement.id='touchdevice';
}
var preventOnDropEvent=function(element){
    element[0].ondragover=function(){
        return false;
    };
    
    element[0].ondragleave=function(){
        return false;
    };
    
    element[0].ondrop=function(event){
        if(event)event.preventDefault?event.preventDefault():(event.returnValue=false);
    };

};

var setMultipleFlag=function(){
    params.fileInput.attr('multiple','multiple');
};

that.UseFileApi=isBrowserSupportFileApi&&isBrowserSupportFormData;
that.CountSendFiles=function(files){
    return that.UseFileApi?files.length:1;
};

that.SendImages=function(paramsObj){
    var mergedParams=$.extend({},params,paramsObj);
    var con=new connection(mergedParams);
    if(that.UseFileApi)
        con.SendFilesWithFileApi(mergedParams.files,mergedParams.fileCount);else
        con.SendFileWithAjaxForm();
};

that.Init=function(paramObj){
    $.extend(params,paramObj);
    preventOnDropEvent($('body'));
    preventOnDropEvent(parent.$('body'));
    if(that.UseFileApi){
        setMultipleFlag();
        (new dragdrop()).Init(params.onImageDropEvent,params.dropZone,params.dropNotify);
    }
};

};

Utils.Uploader.Element=function(){
    var that=this;
    var isMessage=false;
    var item=$('<div class="iuploader-item"><span class="iuploader-highlight-item-brd"></span></div>');
    var container=$('<div class="iuploader-item-content">');
    var cross=$('<div class="iuploader-close"><div class="iuploader-close-in"></div></div>');
    container.appendTo(item);
    cross.appendTo(item);
    that.GetHtml=function(){
        return item;
    };
    
    that.SetContent=function(newContent){
        container.empty();
        for(var i=0,arlen=arguments.length;i<arlen;i++){
            arguments[i].appendTo(container);
        }
        };
        
that.AppendContent=function(content){
    content.appendTo(container);
};

that.IsMessage=function(){
    return isMessage;
};

that.IsActive=function(){
    return item.hasClass('iuploader-highlight-item');
};

that.MakeActive=function(){
    item.addClass('iuploader-highlight-item');
};

that.Highlight=function(){
    item.addClass("iuploader-highlight-item-set");
};

that.Unhighlight=function(){
    item.removeClass("iuploader-highlight-item-set");
};

that.onClick=function(handler){
    item.click(function(){
        handler(that);
    });
};

that.onClose=function(handler){
    cross.click(function(){
        handler(that);
    });
};

that.MakeWide=function(){
    item.addClass('iuploader-item-msg');
};

that.MakeError=function(text,fileName){
    var error=$('<div class="iuploader-item-msg-in"><span class="red">'+text+'</span></div>');
    if(fileName)
        error.prepend($('<div>'+fileName+'</div>'));
    that.MakeWide();
    isMessage=true;
    that.SetContent(error);
};

};

Utils.LotImageUploader=function(){
    var that=this;
    var core=null;
    var params={
        maxFileCount:7,
        files:null,
        imageIdName:null,
        imageListContainer:null,
        onImageChangeHandler:null,
        idsHandler:null
    };
    
    var elements=[];
    var selected=null;
    var onSortDragEnd=false;
    var select=function(element){
        selected=element;
        selected.Highlight();
    };
    
    var deselect=function(){
        selected.Unhighlight();
        selected=null;
    };
    
    var onImageClose=function(element){
        remove(element);
    };
    
    var onImageClick=function(element){
        if(element.IsMessage()){
            remove(element);
            return;
        }
        if(!element.IsActive())
            return;
        var elemNode=element.GetHtml();
        if(onSortDragEnd){
            onSortDragEnd=false;
        }else if(selected){
            swap(selected,element);
        }else{
            select(element);
        }
    };
    
var showIntro=function(){
    params.dropZone.addClass('iuploader-on-intro');
};

var hideIntro=function(){
    params.dropZone.removeClass('iuploader-on-intro');
};

var createElement=function(content){
    var element=new Utils.Uploader.Element(content);
    elements.push(element);
    toTheEnd(element);
    element.onClick(onImageClick);
    element.onClose(onImageClose);
    return element;
};

var getElementCount=function(){
    var i=0,count=0;
    while(i<elements.length){
        if(elements[i])
            count++;
        i++;
    }
    return count;
};

var getImageCount=function(){
    var i=0,count=0;
    while(i<elements.length){
        if(elements[i]&&!elements[i].IsMessage())
            count++;
        i++;
    }
    return count;
};

var remove=function(element){
    if(!element)return;
    elements[elements.indexOf(element)]=null;
    element.GetHtml().remove();
    if(getElementCount()===0)
        showIntro();
    onImagesChange();
};

var removeByIndex=function(index){
    var element=elements[index];
    remove(element);
};

var toTheEnd=function(element){
    if(params.insertBeforeElement){
        element.GetHtml().insertBefore(params.insertBeforeElement);
    }
    else{
        if(params.imageListContainer)params.imageListContainer.prepend(element.GetHtml());
    }
};

var swap=function(element1,element2){
    deselect();
    if(element1!=element2){
        var html1=element1.GetHtml();
        var html2=element2.GetHtml();
        var swapDummy=$('<div>').insertBefore(html1);
        html1.insertBefore(html2);
        swapDummy.replaceWith(html2);
    }
    onImagesChange();
};

var addImage=function(index,id,url){
    var element=elements[index];
    if(element){
        var img=$('<img>').addClass("iuploader-img").attr({
            src:url
        });
        var inp=$('<input>').attr({
            type:"hidden"
        }).val(id);
        element.SetContent(img,inp);
        element.MakeActive();
    }
    onImagesChange();
};

var addError=function(index,text,fileName){
    var element=elements[index];
    if(element){
        element.MakeError(text,fileName);
        toTheEnd(element);
    }
    onImagesChange();
};

var addWarning=function(index,id,url,text){
    addImage(index,id,url);
    var element=elements[index];
    if(element){
        var html=$('<span class="item-msg-txt">');
        html.text(text);
        element.AppendContent(html);
        element.MakeWide();
    }
};

var addLoaders=function(count){
    hideIntro();
    for(var i=0;i<count;i++){
        var element=createElement();
        var loader=$('<div class="iuploader-item-load">');
        element.SetContent(loader);
    }
    onImagesChange();
};

var onImagesChange=function(){
    if(!params.imageIdName)return;
    changeCount();
    if(params.imageListContainer){
        if(getElementCount()>0)
            params.imageListContainer.addClass('iuploader-items-cnt-pad');else
            params.imageListContainer.removeClass('iuploader-items-cnt-pad');
    }
    var imgcount=getImageCount();
    if(params.onImageChangeHandler)
        params.onImageChangeHandler(imgcount,params.maxFileCount);
    var inputs=$('input[type="hidden"]').clone();
    var i=0;
    var input;
    while(input=inputs[i]){
        $(input).attr("name",params.imageIdName+"["+i+"]");
        i++;
    }
    params.idsHandler(inputs,imgcount==i);
};

var changeCount=function(){
    params.currentImageCountText.text(getImageCount());
};

var onFileInputChange=function(event){
    if(event)event.preventDefault?event.preventDefault():(event.returnValue=false);
    that.SendImages(params.fileInput[0].files);
};

that.AddImages=function(files){
    if(!files||files.length==0)
        return;
    addLoaders(files.length);
    var i=0;
    while(i<files.length){
        var file=files[i];
        addImage(i,file.Id,file.Url);
        i++;
    }
};

that.SendImages=function(files){
    if(!core)return false;
    var lastIndex=elements.length;
    var fileCount=core.CountSendFiles(files);
    var imageCount=getImageCount();
    var showError=function(image){
        addError(lastIndex+image.Index,image.ErrorText,image.Name);
    };
    
    var showImage=function(image){
        addImage(lastIndex+image.Index,image.Id,image.Url);
    };
    
    var showWarning=function(image){
        addWarning(lastIndex+image.Index,image.Id,image.Url,image.ErrorText);
    };
    
    var showGlobalError=function(obj){
        var index=lastIndex;
        if(obj.indexOffset)
            index=index+obj.indexOffset;
        var count=index+fileCount;
        if(obj.excessFileCount)
            count=index+obj.excessFileCount;
        addError(index,obj.errorText);
        for(var i=index+1;i<count;i++){
            removeByIndex(i);
        }
        };
        
addLoaders(fileCount);
if(params.maxFileCount>0)
    if(fileCount+imageCount>params.maxFileCount){
        var excessFileCount=fileCount+imageCount-params.maxFileCount;
        fileCount=fileCount-excessFileCount;
        showGlobalError({
            errorText:'аЁаЛаИбаКаОаМ аМаНаОаГаО баАаЙаЛаОаВ',
            indexOffset:fileCount,
            excessFileCount:excessFileCount
        });
        files.length=fileCount;
        if(fileCount<=0)
            return;
    }
var onSuccess=function(images){
    var index=0;
    var image;
    while(image=images[index]){
        switch(image.Status){
            case'Error':
                showError(image);
                break;
            case'Warning':
                showWarning(image);
                break;
            case'Success':
                showImage(image);
                break;
        }
        index++;
    }
};

core.SendImages({
    files:files,
    fileCount:fileCount,
    OnSuccess:onSuccess,
    OnError:showError,
    OnGlobalError:showGlobalError
});
};

that.Init=function(paramsObj){
    $.extend(params,paramsObj);
    params.onImageDropEvent=that.SendImages;
    core=new Utils.Uploader.Core();
    core.Init(params);
    if(!core)
        return false;
    if(params.maxFileCount>0)
        params.maxImageCountText.text(params.maxFileCount);
    if(params.fileInput)params.fileInput.change(onFileInputChange);
    params.imageListContainer.sortable({
        update:onImagesChange,
        placeholder:'iuploader-item-drop-place',
        items:"div.iuploader-item",
        stop:function(event,ui){
            onSortDragEnd=true
            }
        }).disableSelection();
that.AddImages(params.files);
return true;
};

};

Utils.ImportImageUploader=function(){
    var that=this;
    var core=null;
    var params={
        files:null,
        onStartSendImages:null,
        onFinshSendImages:null,
        imageIdName:null,
        onImageChangeHandler:null,
        idsHandler:null,
        errorContainer:null,
        successContainer:null,
        msgContainer:null,
        onSuccess:null
    };
    
    var onFileInputChange=function(event){
        if(event)event.preventDefault?event.preventDefault():(event.returnValue=false);
        that.SendImages(params.fileInput[0].files);
    };
    
    var error=function(text,fileName){
        if(params.errorContainer){
            var errorMsg=$('<div class="iuploader-item-msg-in"><span class="red">'+text+'</span></div>');
            if(fileName)
                errorMsg.prepend($('<div>'+fileName+'</div>'));
            params.errorContainer.append(errorMsg);
            params.errorContainer.show();
        }
    };
    
var success=function(Id,Url,fileName){
    if(params.successContainer){
        var successMsg=$('<div class="iuploader-item-msg-in"><span class="blue">ааАаГббаЖаЕаН '+fileName+'</span></div>');
        params.successContainer.append(successMsg);
        params.successContainer.show();
        if(params.onSuccess)params.onSuccess(Id,Url,fileName);
    }
};

that.SendImages=function(files){
    if(!core)return false;
    var filesCountSend=core.CountSendFiles(files);
    var showError=function(image){
        error(image.ErrorText,image.Name);
        filesCountSend-=1;
        if(filesCountSend<1){
            if(params.onFinshSendImages)params.onFinshSendImages();
            filesCountSend=1;
        }
    };
    
var showImage=function(image){
    success(image.Id,image.Url,image.Name);
    filesCountSend-=1;
};

var showGlobalError=function(obj){
    if(obj.errorText)error(obj.errorText);
    filesCountSend-=(obj.fileCountError)?obj.fileCountError:1;
    if(filesCountSend<1&&params.onFinshSendImages)params.onFinshSendImages();
};

if(params.msgContainer){
    params.msgContainer.empty();
    params.msgContainer.append($('<div class="iuploader-item-msg-in"><span class="blue">аЄаАаЙаЛаОаВ аЗаАаГббаЖаАаЕббб: '+core.CountSendFiles(files)+'</span></div>'));
}
if(params.onStartSendImages){
    params.onStartSendImages();
}
var onSuccess=function(images){
    var index=0;
    var image;
    while(image=images[index]){
        switch(image.Status){
            case'Error':case'Warning':
                showError(image);
                if(params.msgContainer)
                params.msgContainer.empty();
            break;
            case'Success':
                showImage(image);
                if(params.msgContainer)
                params.msgContainer.empty();
            break;
        }
        index++;
    }
    if(filesCountSend<1&&params.onFinshSendImages)params.onFinshSendImages();
};

core.SendImages({
    files:files,
    fileCount:core.CountSendFiles(files),
    OnSuccess:onSuccess,
    OnError:showError,
    OnGlobalError:showGlobalError
});
};

that.Init=function(paramsObj){
    $.extend(params,paramsObj);
    params.onImageDropEvent=that.SendImages;
    core=new Utils.Uploader.Core();
    core.Init(params);
    if(!core)
        return false;
    if(params.fileInput)params.fileInput.change(onFileInputChange);
    return true;
};

};