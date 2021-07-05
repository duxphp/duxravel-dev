<script src="https://unpkg.com/vue@3"></script>
<script src="https://unpkg.com/sortablejs@1"></script>
<script src="https://unpkg.com/vuedraggable@4"></script>


<div class="p-6 pb-0" id="counter">
    <div class="bg-white rounded shadow p-6 mb-4">
        <div class="grid grid-cols-3  gap-4 ">
            <div>
                <label class="form-label block mb-2 text-gray-700 ">功能名 (英文)
                </label>
                <div>
                    <input type="text" class="form-input" v-model="info.class" placeholder="请输入功能名">
                </div>
            </div>
            <div>
                <label class="form-label block mb-2 text-gray-700 ">功能名称 (中文)
                </label>
                <div>
                    <input type="text" class="form-input" v-model="info.name" placeholder="请输入功能名称">
                </div>
            </div>
            <div>
                <label class="form-label block mb-2 text-gray-700 ">所属应用
                </label>
                <div class="flex gap-4">
                    <select class="form-select flex-grow" v-model="info.app">
                        @foreach($appList as $vo)
                            <option value="{{$vo}}">{{$vo}}</option>
                        @endforeach
                    </select>
                    <div>
                        <button class="btn-blue flex-none" @click="submit" type="button">保存</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="tabs">
        <ul class="tabs-nav">
            <li>
                <a class="tabs-item" :class="{'tabs-active': tab === 0}" href="javascript:;" @click="tab = 0">详情表单</a>
            </li>
            <li>
                <a class="tabs-item" :class="{'tabs-active': tab === 1}" href="javascript:;" @click="tab = 1">列表表格</a>
            </li>
            <li>
                <a class="tabs-item" :class="{'tabs-active': tab === 2}" href="javascript:;" @click="tab = 2">数据模型</a>
            </li>
        </ul>

        <div>
            <div v-if="tab === 0">
                <div class="flex form-edit p-6 space-x-4">
                    <div class="flex-none w-56 self-start">
                        <div class="bg-white border border-b-0 border-r-0 border-gray-300">
                            <div class="text-base p-4 border-b border-gray-300 border-r border-gray-300  bg-gray-200">
                                表单组件
                            </div>
                            <ul class="app-package flex flex-wrap ">
                                <li class="cursor-pointer hover:bg-gray-200 w-1/2 border-b border-r border-gray-300"
                                    v-for="(item, index) in formPackage" @click="addFormPackage(index, $event)">
                                    <div class="flex flex-col items-center py-4">
                                        <div><i class="w-6 h-6 leading-6 text-center text-lg text-gray-700"
                                                :class="item.icon"></i></div>
                                        <div class="mt-2 text-gray-500">@{{ item.name }}</div>
                                    </div>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <div class="flex-grow bg-white border border-gray-300 self-start">
                        <div class="text-base p-4 border-b border-gray-300 border-gray-200 bg-gray-200">设计区域</div>


                        <div class="flex justify-center items-center p-10" v-if="!formData.length">
                            <div class="text-base text-gray-500 flex flex-col justify-center items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                     viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                </svg>
                                <div class="mt-4">请点击左边工具添加元素</div>
                            </div>
                        </div>

                        <div v-if="formData.length">
                            <div class="text-base flex-grow p-6 pb-0">@{{ info.name }}</div>
                            <ul class="app-form p-6 flex flex-col space-y-2 min-h-">
                                <draggable v-model="formData" @start="drag=true" @end="drag=false">
                                    <template #item="{element, index}">
                                        <div class="border border-gray-400 border-dashed"
                                             :class="{'border-blue-900' : formItemActive === index}"
                                             @click="editForm(index, $event)" ref="formItem">
                                            <div class="flex items-center p-4 space-x-2">
                                                <label class="flex-none w-24 truncate">@{{ element.name }}</label>
                                                <div class="pl-6 flex-grow">
                                                    <component :is="formPackage[element.type].component"
                                                               :params="element"></component>
                                                </div>
                                                <div class="flex-none">
                                                    <span class="btn-red" @click="delForm(index)">删除</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </ul>
                        </div>
                    </div>
                    <div class="flex-none w-56 self-start" v-if="Object.keys(formItem).length > 0">
                        <div class="bg-white border border-gray-300" @click="$event.stopPropagation()">
                            <div class="text-base p-4 border-b border-gray-300 border-gray-200 bg-gray-200">元素配置</div>
                            <div class="p-4 flex flex-col space-y-4">
                                <div>
                                    <label class="mb-2 block text-gray-500">标题</label>
                                    <input class="form-input" type="text" placeholder="请输入元素标题" v-model="formItem.name">
                                </div>
                                <div>
                                    <label class="mb-2 block text-gray-500">字段名</label>
                                    <input class="form-input" type="text" placeholder="请输入字段名称"
                                           v-model="formItem.field">
                                </div>
                                <div>
                                    <label class="mb-2 block text-gray-500">关联字段名</label>
                                    <input class="form-input" type="text" placeholder="" v-model="formItem.hasField">
                                    <div class="mt-2 text-gray-500">可选，多态关联字段</div>
                                </div>
                                <div v-for="item in formPackage[formItem.type].options">
                                    <div v-if="item.type === 'list'">
                                        <div class="mb-2 block text-gray-500 flex">
                                            <div class="flex-grow">@{{ item.name }}</div>
                                            <div class="flex-none text-blue-900 cursor-pointer hover:underline"
                                                 @click="addFormOptions(formItem.data[item.field])">增加
                                            </div>
                                        </div>
                                        <template v-for="(vo, key) in formItem.data[item.field]">
                                            <div class="flex gap-4 items-center mt-2">
                                                <input class="form-input flex-grow"
                                                       v-model="formItem.data[item.field][key]">
                                                <div class="flex-none text-blue-900 cursor-pointer hover:underline"
                                                     @click="delFormOptions(formItem.data[item.field], key)">删除
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div v-if="item.type === 'textarea'">
                                        <label class="mb-2 block text-gray-500">@{{ item.name }}</label>
                                        <textarea class="form-input" rows=5 :name="item.field">@{{formItem.data[item.field]}} </textarea>
                                    </div>
                                    <div v-if="item.type === 'text'">
                                        <label class="mb-2 block text-gray-500">@{{ item.name }}</label>
                                        <input class="form-input" v-model="formItem.data[item.field]">
                                    </div>
                                    <div v-if="item.type === 'radio'" class="flex flex-col space-y-2">
                                        <label class="mb-2 block text-gray-500">@{{ item.name }}</label>
                                        <label v-for="(value, key) in item.data">
                                            <input class="form-radio mr-2" type="radio"
                                                   v-model="formItem.data[item.field]" :value="key">
                                            @{{ value }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="tab === 1" class="p-6">
                <div class="flex gap-4">
                    <div class="flex-none w-56 self-start">
                        <div class="bg-white border border-b-0 border-r-0 border-gray-300">
                            <div class="text-base p-4 border-b border-gray-300 border-r border-gray-300  bg-gray-200">
                                表格筛选
                            </div>
                            <ul class="app-package flex flex-wrap ">
                                <template v-for="(item, index) in tablePackage.filter">
                                    <li class="cursor-pointer hover:bg-gray-200 w-1/2 border-b border-r border-gray-300"
                                        @click="addTablePackage('filter', index, $event)">
                                        <div class="flex flex-col items-center py-4">
                                            <div><i class="w-6 h-6 leading-6 text-center text-lg text-gray-700"
                                                    :class="item.icon"></i></div>
                                            <div class="mt-2 text-gray-500">@{{ item.name }}</div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                        <div class="bg-white border border-b-0 border-r-0 border-gray-300 mt-2">
                            <div class="text-base p-4 border-b border-gray-300 border-r border-gray-300  bg-gray-200">
                                表格列组件
                            </div>
                            <ul class="app-package flex flex-wrap ">
                                <template v-for="(item, index) in tablePackage.column">
                                    <li class="cursor-pointer hover:bg-gray-200 w-1/2 border-b border-r border-gray-300"
                                        @click="addTablePackage('column', index, $event)">
                                        <div class="flex flex-col items-center py-4">
                                            <div><i class="w-6 h-6 leading-6 text-center text-lg text-gray-700"
                                                    :class="item.icon"></i></div>
                                            <div class="mt-2 text-gray-500">@{{ item.name }}</div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>

                        <div class="bg-white border border-b-0 border-r-0 border-gray-300 mt-2">
                            <div class="text-base p-4 border-b border-gray-300 border-r border-gray-300  bg-gray-200">
                                表格动作
                            </div>
                            <ul class="app-package flex flex-wrap ">
                                <template v-for="(item, index) in tablePackage.action">
                                    <li class="cursor-pointer hover:bg-gray-200 w-1/2 border-b border-r border-gray-300"
                                        @click="addTablePackage('action', index, $event)">
                                        <div class="flex flex-col items-center py-4">
                                            <div><i class="w-6 h-6 leading-6 text-center text-lg text-gray-700"
                                                    :class="item.icon"></i></div>
                                            <div class="mt-2 text-gray-500">@{{ item.name }}</div>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <div class="flex-grow bg-white border border-gray-300 self-start">
                            <div class="text-base p-4 border-b border-gray-300 border-gray-200 bg-gray-200">设计区域</div>

                            <div class="flex justify-center items-center p-10"
                                 v-if="!tableData.action.length && !tableData.filter.length && !tableData.column.length">
                                <div class="text-base text-gray-500 flex flex-col justify-center items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <div class="mt-4">请点击左边工具添加元素</div>
                                </div>
                            </div>

                            <div class="p-6"
                                 v-if="tableData.action.length || tableData.filter.length || tableData.column.length">
                                <div class="pb-2 flex gap-2 items-center">
                                    <div class="text-base flex-grow">@{{ info.name }}</div>
                                    <div class="flex-none">
                                        <draggable v-model="tableData.action" @start="drag=true" @end="drag=false"
                                                   class="flex-none flex flex-wrap gap-2 self-start">
                                            <template #item="{element, index}">
                                                <div @click="editTable('action', index, $event)">
                                                    <component :is="tablePackage.action[element.type].component"
                                                               :params="element"></component>
                                                </div>
                                            </template>
                                        </draggable>
                                    </div>
                                </div>

                                <draggable v-model="tableData.filter" @start="drag=true" @end="drag=false"
                                           class=" pb-4 flex  flex-wrap gap-2 self-start">
                                    <template #item="{element, index}">
                                        <div @click="editTable('filter', index, $event)">
                                            <component :is="tablePackage.filter[element.type].component"
                                                       :params="element"></component>
                                        </div>
                                    </template>
                                </draggable>

                                <draggable v-model="tableData.column" @start="drag=true" @end="drag=false"
                                           class="flex">
                                    <template #item="{element, index}">
                                        <div class="flex-grow  border border-gray-400 border-dashed bg-white"
                                             :class="{'border-blue-900' : tableItem.package === 'column' && tableItemActive === index}"
                                             @click="editTable('column', index, $event)">
                                            <div
                                                class="bg-gray-200 border-b border-gray-300 py-3 px-4 text-gray-600">
                                                @{{ element.name }}
                                            </div>
                                            <div
                                                class="border-b border-gray-300  py-3 px-4 h-20 items-center flex justify-start">
                                                <component :is="tablePackage.column[element.type].component"
                                                           :params="element"></component>
                                            </div>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>
                    </div>
                    <div class="flex-none w-56 self-start" v-if="Object.keys(tableItem).length > 0">
                        <div class="bg-white border border-gray-300" @click="$event.stopPropagation()">
                            <div class="text-base p-4 border-b border-gray-300 border-gray-200 bg-gray-200 flex">
                                <div class="flex-grow">元素配置</div>
                                <div class="flex-none">
                                    <button type="button" class="text-red-900 text-sm"
                                            @click="delTable(tableItem.package)">删除
                                    </button>
                                </div>
                            </div>
                            <div class="p-4 flex flex-col space-y-4">

                                <div v-if="tableItem.package === 'action'">
                                    <label class="mb-2 block text-gray-500">名称</label>
                                    <input class="form-input" type="text" placeholder="" v-model="tableItem.name">
                                </div>

                                <div v-if="tableItem.package === 'column'">
                                    <label class="mb-2 block text-gray-500">标题</label>
                                    <input class="form-input" type="text" placeholder="" v-model="tableItem.name">
                                </div>
                                <div v-if="tableItem.package === 'column'">
                                    <label class="mb-2 block text-gray-500">字段名</label>
                                    <input class="form-input" type="text" placeholder="" v-model="tableItem.field"
                                           list="field-list">
                                    <datalist id="field-list">
                                        <option v-for="item in data" :value="item.field"></option>
                                    </datalist>
                                </div>

                                <div v-if="tableItem.package === 'filter'">
                                    <label class="mb-2 block text-gray-500">标题</label>
                                    <input class="form-input" type="text" placeholder="" v-model="tableItem.name">
                                </div>
                                <div v-if="tableItem.package === 'filter'">
                                    <label class="mb-2 block text-gray-500">筛选字段</label>
                                    <input class="form-input" type="text" placeholder="" v-model="tableItem.field"
                                           list="field-list">
                                    <datalist id="field-list">
                                        <option v-for="item in data" :value="item.field"></option>
                                    </datalist>
                                </div>

                                <div v-for="item in tablePackage[tableItem.package][tableItem.type].options">
                                    <div v-if="item.type === 'array'">
                                        <div class="mb-2 block text-gray-500 flex">
                                            <div class="flex-grow">@{{ item.name }}</div>
                                            <div class="flex-none text-blue-900 cursor-pointer hover:underline"
                                                 @click="addFormOptions(tableItem.data[item.field], {key: '', value: ''})">
                                                增加
                                            </div>
                                        </div>
                                        <template v-for="(vo, key) in tableItem.data[item.field]">
                                            <div class="flex gap-4 items-center mt-2">
                                                <input class="form-input flex-grow"
                                                       v-model="tableItem.data[item.field][key].key">
                                                <input class="form-input flex-grow"
                                                       v-model="tableItem.data[item.field][key].value">
                                                <div class="flex-none text-blue-900 cursor-pointer hover:underline"
                                                     @click="delFormOptions(tableItem.data[item.field], key)">删除
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div v-if="item.type === 'list'">
                                        <div class="mb-2 block text-gray-500 flex">
                                            <div class="flex-grow">@{{ item.name }}</div>
                                            <div class="flex-none text-blue-900 cursor-pointer hover:underline"
                                                 @click="addFormOptions(tableItem.data[item.field])">增加
                                            </div>
                                        </div>
                                        <template v-for="(vo, key) in tableItem.data[item.field]">
                                            <div class="flex gap-4 items-center mt-2">
                                                <input class="form-input flex-grow"
                                                       v-model="tableItem.data[item.field][key]">
                                                <div class="flex-none text-blue-900 cursor-pointer hover:underline"
                                                     @click="delFormOptions(tableItem.data[item.field], key)">删除
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <div v-if="item.type === 'textarea'">
                                        <label class="mb-2 block text-gray-500">@{{ item.name }}</label>
                                        <textarea class="form-input" rows=5 :name="item.field">@{{tableItem.data[item.field]}} </textarea>
                                    </div>
                                    <div v-if="item.type === 'text'">
                                        <label class="mb-2 block text-gray-500">@{{ item.name }}</label>
                                        <input class="form-input" v-model="tableItem.data[item.field]">
                                    </div>
                                    <div v-if="item.type === 'radio'" class="flex flex-col space-y-2">
                                        <label class="mb-2 block text-gray-500">@{{ item.name }}</label>
                                        <label v-for="(value, key) in item.data">
                                            <input class="form-radio mr-2" type="radio"
                                                   v-model="tableItem.data[item.field]" :value="key">
                                            @{{ value }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="tab === 2" class="p-6 flex flex-col gap-4">

                <div class="flex items-center">
                    <div class="flex-grow flex gap-4 items-center">
                        <label class="flex gap-2 items-center"><input type="checkbox" class="form-checkbox"
                                                                      v-model="dataFun.time" value="1"> <span>时间</span></label>
                        <label class="flex gap-2 items-center"><input type="checkbox" class="form-checkbox"
                                                                      v-model="dataFun.del" value="1"> <span>软删除</span></label>
                        <label class="flex gap-2 items-center"><input type="checkbox" class="form-checkbox"
                                                                      v-model="dataFun.tree" value="1">
                            <span>树形结构</span></label>
                        <label class="flex gap-2 items-center"><input type="checkbox" class="form-checkbox"
                                                                      v-model="dataFun.visitor" value="1">
                            <span>访客统计</span></label>
                        <label class="flex gap-2 items-center"><input type="checkbox" class="form-checkbox"
                                                                      v-model="dataFun.form" value="1">
                            <span>表单关联</span></label>
                        <label class="flex gap-2 items-center"><input type="checkbox" class="form-checkbox"
                                                                      v-model="dataFun.tag" value="1">
                            <span>tag 标签</span></label>
                    </div>
                    <div class="flex-none">
                        <button type="button" class="btn-blue" @click="addData()">增加</button>
                    </div>
                </div>
                <draggable v-model="data" @start="drag=true" @end="drag=false" tag="table" class="table-box">
                    <template #header>
                        <thead>
                        <tr>
                            <th>字段名称</th>
                            <th>字段类型</th>
                            <th>Unsigned</th>
                            <th>NULL</th>
                            <th>长度</th>
                            <th>索引类型</th>
                            <th>默认值</th>
                            <th>注释</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                    </template>
                    <tbody>
                    <template #item="{element, index}">
                        <tr>
                            <td class=" whitespace-nowrap">
                                <input type="text" class="form-input" v-model="element.field">
                            </td>
                            <td class=" whitespace-nowrap">
                                <select v-model="element.type" class="form-select" :disabled="element.preset">
                                    <optgroup label="字符串">
                                        <option value="CHAR">CHAR</option>
                                        <option value="VARCHAR">VARCHAR</option>
                                        <option value="BLOB">BLOB</option>
                                        <option value="TEXT">TEXT</option>
                                        <option value="MEDIUMTEXT">MEDIUMTEXT</option>
                                        <option value="LONGTEXT">LONGTEXT</option>
                                        <option value="JSON">JSON</option>
                                    </optgroup>
                                    <optgroup label="数值">
                                        <option value="INTEGER">INTEGER</option>
                                        <option value="BIGINT">BIGINT</option>
                                        <option value="TINYINT">TINYINT</option>
                                        <option value="SMALLINT">SMALLINT</option>
                                        <option value="MEDIUMINT">MEDIUMINT</option>
                                        <option value="FLOAT">FLOAT</option>
                                        <option value="DOUBLE">DOUBLE</option>
                                        <option value="DECIMAL">DECIMAL</option>
                                    </optgroup>
                                    <optgroup label="日期时间">
                                        <option value="DATE">DATE</option>
                                        <option value="TIME">TIME</option>
                                        <option value="YEAR">YEAR</option>
                                        <option value="DATETIME">DATETIME</option>
                                        <option value="TIMESTAMP">TIMESTAMP</option>
                                    </optgroup>
                                </select>
                            </td>
                            <td class=" whitespace-nowrap">
                                <input type="checkbox" class="form-checkbox" v-model="element.unsigned" value="1"
                                       :disabled="element.preset">
                            </td>
                            <td class=" whitespace-nowrap">
                                <input type="checkbox" class="form-checkbox" v-model="element.null" value="1"
                                       :disabled="element.preset">
                            </td>
                            <td class=" whitespace-nowrap">
                                <input type="text" class="form-input" v-model="element.len"
                                       :disabled="element.preset">
                            </td>
                            <td class=" whitespace-nowrap">
                                <select class="form-select" v-model="element.index" :disabled="element.preset">
                                    <option>无</option>
                                    <option value="PRIMARY">PRIMARY</option>
                                    <option value="NORMAL">NORMAL</option>
                                    <option value="UNIQUE">UNIQUE</option>
                                </select>
                            </td>
                            <td class=" whitespace-nowrap">
                                <input type="text" class="form-input" v-model="element.default"
                                       :disabled="element.preset">
                            </td>
                            <td class=" whitespace-nowrap">
                                <input type="text" class="form-input" v-model="element.name">
                            </td>
                            <td class=" whitespace-nowrap">
                                <span class="cursor-pointer" @click="!element.preset && delData(index)"
                                      :class="{'text-gray-500': element.preset, 'text-blue-900': !element.preset}">删除</span>
                            </td>
                        </tr>
                    </template>
                    </tbody>
                </draggable>

            </div>
        </div>

    </div>
</div>

<script type="application/javascript">

    const defaultData = @json($info->data)

    const tablePackage = {
        column: {
            text: {
                name: '文本',
                icon: 'fa fa-font',
                component: {
                    props: ['params'],
                    template: `<div class="flex items-center">文本</div>`
                },
                data: {},
            },
            imageText: {
                name: '图文',
                icon: 'fa fa-file-alt',
                component: {
                    props: ['params'],
                    template: `<div class="flex items-center"><div class="flex-none"><div class="relative w-12 h-12 border border-gray-400 rounded bg-cover block" style="background-image: url('{{route('service.image.placeholder', ['w' => 100, 'h' => 100, 't' => '图片'])}}')"></div></div><div class="flex-grow ml-2"><div>标题</div><div class="text-gray-400">副标题</div></div></div>`
                },
                data: {
                    image: '',
                    desc: ''
                },
                options: [
                    {
                        name: '图片字段',
                        field: 'image',
                        type: 'text',
                    },
                    {
                        name: '描述字段',
                        field: 'desc',
                        type: 'text',
                    },
                ],
            },
            status: {
                name: '状态展示',
                icon: 'fa fa-tags',
                component: {
                    props: ['params'],
                    template: `<div class="flex items-center"><span class="select-none rounded border text-sm px-2 py-1 bg-blue-900 text-white">状态</span></div>`
                },
                data: {},
                options: [],
            },
            toggle: {
                name: '状态切换',
                icon: 'fa fa-toggle-on',
                component: {
                    props: ['params'],
                    template: `<label class="form-toggle">
                <input class="form-toggle-input" checked="" type="checkbox" value="1" disabled name="status">
                <span class="form-toggle-label"></span>
                </label>`
                },
                data: {},
                options: [],
            },
            progress: {
                name: '进度条',
                icon: 'fa fa-capsules',
                component: {
                    props: ['params'],
                    template: `<div class="flex items-center w-full">
                    <div class="flex-grow rounded-full h-3 box-border border border-gray-300 relative ">
                        <div class="bg-blue-900  h-3 rounded-l-full absolute  -top-px -left-px" style="width: 16% "></div>
                    </div>
                    <span class="flex-none w-8 ml-4 text-gray-500">16%</span>
                </div>`
                },
                data: {},
                options: [],
            },
            manage: {
                name: '管理列',
                icon: 'fa fa-link',
                component: {
                    props: ['params'],
                    template: `
                        <a class="inline-flex items-center text-blue-900 hover:underline" style=""> 编辑</a><span class="mx-1 text-gray-300"> | </span>    <a class="inline-flex items-center text-blue-900 hover:underline"> 删除</a>
                   `
                },
                data: {},
                options: [],
            },
        },
        action: {
            add: {
                name: '添加',
                icon: 'fa fa-plus',
                component: {
                    props: ['params'],
                    template: `<span class="inline-flex items-center btn-blue">
                            <div class="w-4 h-4 mr-2 ">
                                <svg class="stroke-current w-full h-full" style="" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path d="M12 4V20M20 12L4 12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            @{{ params.name }}
                    </span>`
                },
                data: {},
                options: [],
            },
            export: {
                name: '导出',
                icon: 'fa fa-download',
                component: {
                    props: ['params'],
                    template: `<span class="inline-flex items-center btn-blue">
                            <div class="w-4 h-4 mr-2 ">
                                <i class="fa fa-download"></i>
                            </div>
                            @{{ params.name }}
                    </span>`
                },
                data: {
                    icon: '',
                    route: '',
                    params: [
                        {key: 'id', value: '编号'}
                    ]
                },
                options: [
                    {
                        name: '导出列',
                        field: 'params',
                        type: 'array',
                    },
                ],
            },
            button: {
                name: '按钮',
                icon: 'fa fa-bullseye',
                component: {
                    props: ['params'],
                    template: `<span class="inline-flex items-center btn-blue">
                            @{{ params.name }}
                    </span>`
                },
                data: {
                    icon: '',
                    route: '',
                    params: [
                        {key: '', value: ''}
                    ]
                },
                options: [
                    {
                        name: '图标名',
                        field: 'icon',
                        type: 'text',
                    },
                    {
                        name: '路由名',
                        field: 'route',
                        type: 'text',
                    },
                    {
                        name: '路由参数',
                        field: 'params',
                        type: 'array',
                    },
                ],
            },
            menu: {
                name: '菜单',
                icon: 'fa fa-bars',
                component: {
                    props: ['params'],
                    template: `
                    <div class="relative">
                        <span class="inline-flex items-center btn-blue">
                            @{{ params.name }}
                        </span>
                        <div class="shadow w-40 pt-1 pb-1 mt-2 rounded-sm bg-white" >
                             <div v-for="item in params.data.menu" class="flex p-2 hover:bg-gray-200">@{{ item.key }}</div>
                        </div>
                    </div>`
                },
                data: {
                    menu: [
                        {key: '子菜单', value: 'admin.index'}
                    ]
                },
                options: [
                    {
                        name: '菜单条目',
                        field: 'menu',
                        type: 'array',
                    },
                ],
            },
        },
        filter: {
            text: {
                name: '文本框',
                icon: 'fa fa-pen',
                component: {
                    props: ['params'],
                    template: `<input disabled type="text" class="form-input" :name="params.field" :placeholder="'请输入' + params.name">`
                },
                options: [
                    {
                        name: '筛选类型',
                        field: 'type',
                        type: 'radio',
                        data: {
                            0: '快速',
                            1: '折叠'
                        }
                    },
                ],
                data: {
                    type: 0,
                }
            },
            select: {
                name: '下拉框',
                icon: 'fa fa-list',
                component: {
                    props: ['params'],
                    template: `<select disabled class="form-select" :placeholder="'请选择' + params.name">
                  <option v-for='(item, index) in params.data.options' :value="item.key" >@{{item.value}}</option>
                </select>`
                },
                options: [
                    {
                        name: '筛选类型',
                        field: 'type',
                        type: 'radio',
                        data: {
                            0: '快速',
                            1: '折叠'
                        }
                    },
                    {
                        name: '下拉选项',
                        field: 'options',
                        type: 'array',
                    }
                ],
                data: {
                    type: 0,
                    options: [
                        {key: '0', value: '选项一'}
                    ]
                }
            },
        }
    };

    const formPackage = {
        text: {
            name: '文本框',
            icon: 'fa fa-pen',
            field: 'text',
            fieldType: 'VARCHAR',
            fieldLen: 250,
            component: {
                props: ['params'],
                template: `<input disabled type="text" class="form-input" :name="params.field" :placeholder="'请输入' + params.name">`
            },
            options: [{
                name: '验证',
                field: 'required',
                type: 'radio',
                data: {
                    0: '选填',
                    1: '必填'
                }
            },
                {
                    name: '类型',
                    field: 'type',
                    type: 'radio',
                    data: {
                        'text': '文本',
                        'number': '数字',
                        'email': '邮箱',
                        'tel': '手机号码',
                        'password': '密码',
                        'ip': 'IP地址',
                        'url': '网址',
                        'date': '日期',
                        'time': '时间',
                    }
                }
            ],
            data: {
                required: 0,
                type: 'text'
            }
        },
        select: {
            name: '下拉选择',
            icon: 'fa fa-list',
            field: 'select',
            fieldType: 'INTEGER',
            fieldLen: 10,
            component: {
                props: ['params'],
                template: `<select disabled class="form-select" :placeholder="'请选择' + params.name">
                  <option v-for='(item, index) in params.data.options' :value="index" >@{{item}}</option>
                </select>`
            },
            options: [{
                name: '下拉选项',
                field: 'options',
                type: 'list',
            }],
            data: {
                options: []
            }
        },
        radio: {
            name: '单选项',
            icon: 'fa fa-check-circle',
            field: 'radio',
            fieldType: 'TINYINT',
            fieldLen: 1,
            component: {
                props: ['params'],
                template: `<div class="flex flex-row space-x-4">
                  <label class="block" v-for='(item, index) in params.data.options'>
                      <input class="form-radio mr-2" :checked="index === 0" disabled type="radio" :value="index">
                      @{{item}}
                  </label>
                </div>`
            },
            options: [{
                name: '单选项',
                field: 'options',
                type: 'list',
            }],
            data: {
                options: [
                    '选项一',
                    '选项二'
                ]
            }
        },
        checkbox: {
            name: '多选项',
            icon: 'fa fa-check-square',
            field: 'checkbox',
            fieldType: 'CHAR',
            fieldLen: 50,
            component: {
                props: ['params'],
                template: `<div class="flex flex-row space-x-4">
                  <label class="block" v-for='(item, index) in params.data.options'>
                      <input class="form-checkbox mr-2" disabled type="checkbox" :value="index">
                      @{{item}}
                  </label>
                </div>`
            },
            options: [{
                name: '多选项',
                field: 'options',
                type: 'list',
            }],
            data: {
                options: [
                    '选项一',
                    '选项二'
                ]
            }
        },
        image: {
            name: '图片上传',
            icon: 'fa fa-image',
            field: 'image',
            fieldType: 'VARCHAR',
            fieldLen: 250,
            component: {
                props: ['params'],
                template: `<div class="relative w-24 h-24 border-2 border-gray-400 border-dashed rounded bg-cover bg-center bg-no-repeat block" style="background-image: url('{{route('service.image.placeholder', ['w' => 180, 'h' => 180, 't' => '选择图片'])}}')"></div>`
            },
            options: [{
                name: '验证',
                field: 'required',
                type: 'radio',
                data: {
                    0: '选填',
                    1: '必填'
                }
            },
                {
                    name: '上传方式',
                    field: 'type',
                    type: 'radio',
                    data: {
                        0: '文件管理器',
                        1: '本地上传'
                    }
                },
            ],
            data: {
                required: 0,
                type: 0,
            }
        },
        images: {
            name: '多图片上传',
            icon: 'fa fa-images',
            field: 'images',
            fieldType: 'TEXT',
            fieldLen: 0,
            component: {
                props: ['params'],
                template: `<div class="flex space-x-4" >
                                <div class="relative w-32 h-32 border-2 border-gray-400 border-dashed rounded bg-cover bg-center bg-no-repeat block" style="background-size:90%; background-image:url('{{route('service.image.placeholder',
                    ['w' => 180, 'h' => 180, 't' => '图片'])}}')">
                                </div>
                                <div class="relative w-32 h-32 border-2 border-gray-400 border-dashed rounded bg-cover bg-center bg-no-repeat block">
                                    <div class="text-gray-500 absolute flex items-center justify-center w-full h-full bg-gray-100 bg-opacity-90 rounded cursor-pointer">
                                        <div class="text-base">
                                            上传
                                        </div>
                                    </div>
                                </div>
                            </div>`
            },
            options: [{
                name: '验证',
                field: 'required',
                type: 'radio',
                data: {
                    0: '选填',
                    1: '必填'
                }
            },
                {
                    name: '上传方式',
                    field: 'type',
                    type: 'radio',
                    data: {
                        0: '文件管理器',
                        1: '本地上传'
                    }
                },
                {
                    name: '图片数量',
                    field: 'num',
                    type: 'text',
                },
            ],
            data: {
                type: 0,
                required: 0,
                num: 5
            }
        },
        file: {
            name: '文件上传',
            icon: 'fa fa-file',
            field: 'file',
            fieldType: 'VARCHAR',
            fieldLen: 250,
            component: {
                props: ['params'],
                template: `<div class="form-input-group form-input-group-after">
                                <input type="text" class="form-input" readonly :placeholder="'请选择' + params.name" disabled>
                                <button type="button" class="form-input-label-after focus:outline-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path><polyline points="9 15 12 12 15 15"></polyline><line x1="12" y1="12" x2="12" y2="21"></line></svg>
                                上传
                                </button>
                            </div>`
            },
            options: [{
                name: '验证',
                field: 'required',
                type: 'radio',
                data: {
                    0: '选填',
                    1: '必填'
                }
            },
                {
                    name: '上传方式',
                    field: 'type',
                    type: 'radio',
                    data: {
                        0: '文件管理器',
                        1: '本地上传'
                    }
                },
            ],
            data: {
                type: 0,
                required: 0,
            }
        },
        date: {
            name: '日期时间',
            icon: 'fa fa-calendar-alt',
            field: 'date',
            fieldType: 'INTEGER',
            fieldLen: 10,
            component: {
                props: ['params'],
                template: `<input type="text" class="form-input" :placeholder="'请选择' + params.name">`
            },
            options: [{
                name: '验证',
                field: 'required',
                type: 'radio',
                data: {
                    0: '选填',
                    1: '必填'
                }
            },
                {
                    name: '类型',
                    field: 'type',
                    type: 'radio',
                    data: {
                        'date': '日期',
                        'time': '时间',
                        'datetime': '日期时间',
                        'range': '时间范围',
                    }
                }
            ],
            data: {
                required: 0,
                type: 'date'
            }
        },
        editor: {
            name: '编辑器',
            icon: 'fa fa-edit',
            field: 'editor',
            fieldType: 'LONGTEXT',
            fieldLen: 0,
            component: {
                props: ['params'],
                template: `<textarea class="form-textarea" disabled :placeholder="'请输入' + params.name + '内容'"></textarea>`
            },
            options: [{
                name: '验证',
                field: 'required',
                type: 'radio',
                data: {
                    0: '选填',
                    1: '必填'
                }
            },],
            data: {
                required: 0,
            }
        },
        color: {
            name: '颜色选择',
            icon: 'fas fa-swatchbook',
            field: 'color',
            fieldType: 'CHAR',
            fieldLen: 10,
            component: {
                props: ['params'],
                template: `<div class="flex flex-row space-x-2">
                    <label class="form-color" v-for="(item, key) in ['while', 'black', 'blue', 'yellow', 'green', 'red', 'purple' ]">
                        <input :checked="key === 0" type="radio" style="" disabled>
                        <span class="form-color-show" :class="'bg-' + (item === 'while' || item === 'black' ? item : item + '-900')"></span>
                    </label>
                </div>`
            },
            options: [{
                name: '类型',
                field: 'type',
                type: 'radio',
                data: {
                    'color': '选项颜色',
                    'picker': '自由颜色',
                }
            }],
            data: {
                type: 'color'
            }
        }
    };

    const Counter = {
        mounted() {
            document.addEventListener("click", () => {
                this.formItemActive = false
                this.formItem = {}
                this.tableItemActive = false
                this.tableItem = {}
            });
        },
        data() {
            return {
                drag: false,
                tab: 0,
                info: {
                    name: defaultData ? defaultData.info.name : '',
                    class: defaultData ? defaultData.info.class : '',
                    app: defaultData ? defaultData.info.app : '',
                },
                formData: defaultData ? defaultData.formData : [],
                formItemActive: false,
                formPackage: formPackage,
                formItem: {},
                tableData: {
                    column: defaultData ? defaultData.tableData.column : [],
                    action: defaultData ? defaultData.tableData.action : [],
                    filter: defaultData ? defaultData.tableData.filter : []
                },
                tableItemActive: false,
                tablePackage: tablePackage,
                tableItemIndex: 0,
                tableItem: {},
                data: defaultData ? defaultData.data : [
                    {
                        preset: 1,
                        name: '主键',
                        field: 'id',
                        type: 'INTEGER',
                        len: null,
                        index: 'PRIMARY',
                        unsigned: true,
                        null: false,
                        default: ''
                    }
                ],
                dataFun: defaultData ? defaultData.dataFun : {
                    time: true,
                    del: false,
                    tree: false,
                    visitor: false,
                    form: false,
                    tag: false
                }
            }
        },
        methods: {
            database() {
                let data = {};
                this.formData.forEach(item => {
                    data[item.field] = true;
                    !this.data.some(vo => vo.field === item.field) && this.data.push({
                        name: item.name,
                        field: item.field,
                        type: this.formPackage[item.type].fieldType,
                        len: this.formPackage[item.type].fieldLen,
                        index: '',
                        unsigned: false,
                        null: true,
                        default: null
                    })
                })
                for (let i = 0; i < this.data.length; i++) {
                    if (!data[this.data[i].field] && !this.data[i].preset) {
                        this.data.splice(i, 1)
                        i--
                    }
                }
                console.log(JSON.stringify(this.data, null, 2))
                return this.data;
            },
            addFormPackage(index) {
                this.formData.push(JSON.parse(JSON.stringify({
                    type: index,
                    name: this.formPackage[index].name,
                    field: this.formPackage[index].field,
                    data: this.formPackage[index].data,
                    hasField: '',
                })))
                this.database()
            },
            editForm(index, e) {
                e.stopPropagation()
                this.formItemActive = index
                this.formItem = this.formData[index]
                this.database()
            },
            delForm(index) {
                this.formData.splice(index, 1)
            },
            addFormOptions(options, value) {
                options.push(value || '');
            },
            delFormOptions(options, key) {
                options.splice(key, 1)
            },
            addTablePackage(type, index) {
                this.tableData[type].push(JSON.parse(JSON.stringify({
                    package: type,
                    type: index,
                    name: this.tablePackage[type][index].name,
                    data: this.tablePackage[type][index].data,
                    field: '',
                })));
            },
            editTable(type, index, e) {
                e.stopPropagation()
                this.tableItemActive = index
                this.tableItem = this.tableData[type][index]
            },
            delTable(type) {
                this.tableData[type].splice(this.tableItemActive, 1)
            },
            addData() {
                this.data.push({
                    name: '',
                    field: '',
                    type: 'VARCHAR',
                    len: null,
                    index: '',
                    unsigned: false,
                    null: true,
                    default: null
                })
            },
            delData(index) {
                this.data.splice(index, 1)
            },
            submit: function () {
                let data = {
                    info: this.info,
                    data: this.data,
                    dataFun: this.dataFun,
                    formData: this.formData,
                    tableData: this.tableData
                }
                app.ajax({
                    url: '{{route("admin.dev.app.generateAdmin.save", ['id' => $id])}}',
                    type: 'POST',
                    data: {
                        data: data
                    },
                })
            }
        }
    }

    const vueApp = Vue.createApp(Counter)
    vueApp.component('draggable', window.vuedraggable)
    vueApp.mount('#counter')
</script>
