<template>
    <div class="col-lg-12">
      <div class="card custom-card overflow-hidden">
        <div class="card-body">
          <div class="table-responsive">
            <div id="example1_wrapper" class="table table-bordered text-nowrap">
              <div class="row">
                <div class="col-sm-12 col-md-6">
                  <div class="d-inline-flex p-2">
                    <select name="example1_length" aria-controls="example1" class="form-select form-select select2"
                      v-model="perPage">
                      <option value="5">5</option>
                      <option value="10">10</option>
                      <option value="20">20</option>
                      <option value="50">50</option>
                    </select>
                  </div>
                  <div class="d-inline-flex p-2"><label> items/page </label></div>
                  <div class="dataTables_length" id="example1_length"></div>
                </div>
                <div class="col-sm-12 col-md-6">
                  <div id="example1_filter" class="d-flex justify-content-end">
                    <label>
                      <input v-model="keyword" type="search" class="form-control form-control" placeholder="Search..."
                        aria-controls="example1" />
                    </label>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <table class="table table-bordered border-bottom" id="example1">
                    <thead>
                      <tr>
                        <th v-if="withCheckbox">
                          <input type="checkbox" v-model="checkAll" />
                          Select All
                        </th>
                        <th v-if="buttons.length > 0">Actions</th>
                        <th :style="{ minWidth: '150px' }" v-for="column in columns" :key="column.field">
                          <span class="" @click="sortByColumn(columnsHeaderKey[column.field])">{{ column.name }}</span>
                          <span v-if="
                            columnsHeaderKey[column.field] !== undefined &&
                            (columnsHeaderKey[column.field]['sortable'] ===
                              undefined ||
                              columnsHeaderKey[column.field]['sortable'] ===
                              true) &&
                            columnsHeaderKey[column.field] === sortedColumn
                          ">
                            <i v-if="order === 'asc'" class="fas fa-arrow-up"></i>
                            <i v-else class="fas fa-arrow-down"></i>
                          </span>
                        </th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="data in tableData" :key="data.id" :style="{
                        background: rowColor(data),
                      }">
                        <td v-if="withCheckbox">
                          <input type="checkbox" v-model="data.selected" v-on:change="checkboxChanged()" />
                        </td>
                        <td v-if="buttons.length > 0">
                          <div v-for="button in buttons" :key="button.name">
                            <div v-if="button.kind === 'group'" class="btn-group">
                              <div v-if="
                                data[button.visibility] === undefined ||
                                data[button.visibility]
                              ">
                                <b-button-group>
                                  <b-dropdown size="sm" :variant="button['type']" v-on:click="
                                    $emit(
                                      'button-click',
                                      button['method'],
                                      converToObject(data)
                                    )
                                  " right split :text="button['name']">
                                    <div v-for="groupbutton in button['buttons']" :key="groupbutton['name']">
                                      <b-dropdown-item v-if="
                                        data[groupbutton['visibility']] ===
                                          undefined
                                          ? true
                                          : data[groupbutton['visibility']]
                                      " v-on:click="
                                          $emit(
                                            'button-click',
                                            groupbutton['method'],
                                            converToObject(data)
                                          )
                                        ">{{
                                          groupbutton["name"]
                                        }}</b-dropdown-item>
                                                                          </div>
                                                                        </b-dropdown>
                                                                      </b-button-group>
                                                                    </div>
                                                                  </div>
                                                                  <div v-else>
                                                                    <button v-if="
                                                                      data[button['visibility']] === undefined ||
                                                                      data[button['visibility']]
                                                                    " type="button" style="margin-right: 5px" class="btn btn-sm" :class="{
                                          'btn-success': button['type'] === 'success',
                                          'btn-warning': button['type'] === 'warning',
                                          'btn-danger': button['type'] === 'danger',
                                        }" v-on:click="
                                          $emit(
                                            'button-click',
                                            button['method'],
                                            converToObject(data)
                                          )
                                        ">
                                {{ button["name"] }}
                              </button>
                            </div>
                          </div>
                        </td>
                        <td v-for="column in columns" :key="column.field" @click="dataClicked(column.field, data)">
                          <click-to-edit-component v-if="column.type === 'clickToEdit'" :value="data[column.field]"
                            :identifier="column.field" :object="data" :name="column.name"
                            @onRelease="onClickToEditRelease" />
                          <click-to-edit-textarea-component v-else-if="column.type === 'clickToEditTextarea'"
                            :value="data[column.field]" :identifier="column.field" :object="data" :name="column.name"
                            @onRelease="onClickToEditRelease" />
                          <click-to-edit-date-component v-else-if="column.type === 'clickToEditDate'"
                            :value="data[column.field]" :identifier="column.field" :object="data" :name="column.name"
                            @onRelease="onClickToEditRelease" />
                          <click-to-edit-time-component v-else-if="column.type === 'clickToEditTime'"
                            :value="data[column.field]" :identifier="column.field" :object="data" :name="column.name"
                            @onRelease="onClickToEditRelease" />
                          <click-to-edit-number-component v-else-if="column.type === 'clickToEditNumber'"
                            :value="data[column.field]" :identifier="column.field" :object="data" :name="column.name"
                            @onRelease="onClickToEditRelease" />
                          <click-to-select-component v-else-if="column.type === 'clickToSelect'" :value="data[column.field]"
                            :identifier="column.field" :object="data" :options="column.options" :url="column.url"
                            @onRelease="onClickToEditRelease" />
                          <span v-else>{{ data[column.field] }}</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12 col-md-5">
                  <div v-if="pagination.data !== undefined" class="dataTables_info" id="example1_info" role="status"
                    aria-live="polite">
                    Showing {{ pagination.data.length }} of
                    {{ pagination.total }} entries
                  </div>
                </div>
                <div class="col-sm-12 col-md-7">
                  <div class="
                              dataTables_paginate
                              paging_simple_numbers
                              d-flex
                              justify-content-end
                            " id="example1_paginate">
                    <ul class="pagination">
                      <li class="paginate_button page-item previous" :class="{ disabled: currentPage === 1 }"
                        id="example1_previous">
                        <a href="#" aria-controls="example1" data-dt-idx="0" tabindex="0" class="page-link"
                          @click.prevent="changePage(currentPage - 1)">Previous</a>
                      </li>
                      <li class="paginate_button page-item" v-for="page in pagesNumber" :key="page"
                        :class="{ active: page == pagination.current_page }">
                        <a href="javascript:void(0)" @click.prevent="changePage(page)" aria-controls="example1"
                          data-dt-idx="1" tabindex="0" class="page-link">{{ page }}</a>
                      </li>
                      <li class="paginate_button page-item next" :class="{
                        disabled: currentPage === pagination.last_page,
                      }" id="example1_next">
                        <a href="#" aria-controls="example1" data-dt-idx="7" tabindex="0" class="page-link"
                          @click.prevent="changePage(currentPage + 1)">Next</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script type="text/ecmascript-6">
  import Vue from "vue";
  import BootstrapVue from "bootstrap-vue";
  
  Vue.use(BootstrapVue);
  
  import "bootstrap/dist/css/bootstrap.css";
  import "bootstrap-vue/dist/bootstrap-vue.css";
  
  export default {
    components: {},
    props: {
      fetchUrl: { type: String, required: true },
      columns: { type: Array, required: true },
      buttons: { type: Array, required: false, default: () => [] },
      withCheckbox: { type: Boolean, required: false, default: false },
      defaultSortIndex: { type: Number, required: false },
      defaultSortOrder: { type: String, required: false },
      defaultKeyword: { type: String, required: false },
      hasSearch: { type: Boolean, required: false, default: true },
      tableID: { type: String, required: false, default: "" },
    },
    data() {
      var initialSortIndex = 0;
      if (this.defaultSortIndex !== undefined) {
        initialSortIndex = this.defaultSortIndex;
      }
  
      var initialSortOrder = "asc";
      if (this.defaultSortOrder !== undefined) {
        initialSortOrder = this.defaultSortOrder;
      }
  
      var initialKeyword = "";
      if (this.defaultKeyword !== undefined && this.defaultKeyword !== null) {
        initialKeyword = this.defaultKeyword;
      }
  
      return {
        url: "",
        pagination: {
          meta: { to: 1, from: 1 },
        },
        offset: 4,
        currentPage: 1,
        perPage: 5,
        sortedColumn: this.columns[initialSortIndex],
        order: initialSortOrder,
        keyword: initialKeyword,
        checkAll: false,
        tableData: [],
        tableFields: [],
        tableNames: {},
        cellTypes: {},
        columnsHeaderKey: {},
        editTextData: {
          item: null,
          key: "",
          value: "",
        },
        dropDownData: {
          item: null,
          key: "",
          value: "",
          options: [],
        },
      };
    },
    watch: {
      fetchUrl: {
        handler: function (fetchUrl) {
          this.url = fetchUrl;
          this.fetchData();
        },
        immediate: true,
      },
      keyword: {
        handler: function (keyword) {
          this.currentPage = 1;
          this.fetchData();
        },
        immediate: true,
      },
      perPage: {
        handler: function (perPage) {
          this.fetchData();
        },
        immediate: true,
      },
      checkAll: {
        handler: function (checkAll) {
          for (var i = 0; i < this.tableData.length; i++) {
            var object = this.tableData[i];
            object.selected = checkAll;
            this.checkboxChanged();
          }
        },
      },
      columns: {
        handler: function (columns) {
          if (columns.length > 0) {
            this.sortedColumn = columns[this.defaultSortIndex];
          }
          this.fetchData();
        },
      },
      buttons: {
        handler: function (buttons) {
          this.fetchData();
        },
      },
    },
    created() {
      return this.fetchData();
    },
    computed: {
      /**
       * Get the pages number array for displaying in the pagination.
       * */
      pagesNumber() {
        if (!this.pagination.to) {
          return [];
        }
        let from = this.pagination.current_page - this.offset;
        if (from < 1) {
          from = 1;
        }
        let to = from + this.offset * 2;
        if (to >= this.pagination.last_page) {
          to = this.pagination.last_page;
        }
        let pagesArray = [];
        for (let page = from; page <= to; page++) {
          pagesArray.push(page);
        }
        return pagesArray;
      },
      /**
       * Get the total data displayed in the current page.
       * */
      totalData() {
        return this.pagination.meta.to - this.pagination.meta.from + 1;
      },
    },
    methods: {
      dataClicked(field, data) {
        this.$emit("onDataClicked", field, data);
      },
      rowColor(data) {
        var row_color = "#00000000";
      },
      converToObject(data) {
        var object = { item: data };
        return object;
      },
      editTextField(item, key, value) {
        this.editTextData.item = item;
        this.editTextData.key = key;
        this.editTextData.value = value;
        this.$bvModal.show("editTextFieldModal");
      },
      editDropDown(item, key, value) {
        this.dropDownData.item = item;
        this.dropDownData.key = key;
        this.dropDownData.value = value;
        this.dropDownData.options = this.columnsHeaderKey[key].options;
        this.$bvModal.show("dropDownFieldModal");
      },
      onClickToEditRelease(currentValue, identifier, object, name) {
        this.$emit("clickToEditSaved", object, identifier, currentValue, name);
      },
      saveEditTextField() {
        this.$bvModal.hide("editTextFieldModal");
        this.$emit(
          "editTextSaved",
          this.editTextData.item,
          this.editTextData.key,
          this.editTextData.value
        );
      },
      saveDropDownField() {
        this.$bvModal.hide("dropDownFieldModal");
        this.$emit(
          "editTextSaved",
          this.dropDownData.item,
          this.dropDownData.key,
          this.dropDownData.value
        );
      },
      fetchData() {
        this.tableFields.length = 0;
        this.tableNames = {};
        this.cellTypes = {};
        this.columns.forEach((column) => {
          if (column.hasOwnProperty("show") && column["show"] == false) {
            return;
          }
          this.tableFields.push(column["field"]);
          this.tableNames[column["field"]] = column["name"];
          this.cellTypes[column["field"]] = column["type"];
          this.columnsHeaderKey[column["field"]] = column;
        });
        if (this.buttons.length > 0) {
          this.tableFields.push("actions");
          this.tableNames["actions"] = "Actions";
        }
  
        if (this.url.length == 0) {
          return;
        }
        if (this.sortedColumn === undefined) {
          return;
        }
        let dataFetchUrl = `${this.url}page=${this.currentPage}&column=${this.sortedColumn["field"]}&order=${this.order}&per_page=${this.perPage}`;
        if (this.keyword.length > 0) {
          dataFetchUrl += "&keyword=" + encodeURIComponent(this.keyword);
        }
        axios
          .get(dataFetchUrl)
          .then(({ data }) => {
            this.pagination = data.data;
            this.tableData.length = 0;
            var tempData = [];
            data.data.data.forEach((data) => {
              tempData.push(data);
            });
            this.tableData = tempData;
          })
          .catch((error) => (this.tableData = []));
      },
      /**
       * Get the serial number.
       * @param key
       * */
      serialNumber(key) {
        return (this.currentPage - 1) * this.perPage + 1 + key;
      },
      /**
       * Change the page.
       * @param pageNumber
       */
      changePage(pageNumber) {
        this.currentPage = pageNumber;
        this.fetchData();
      },
      /**
       * Sort the data by column.
       * */
      sortByColumn(column) {
        if (column["sortable"] === false) {
          return;
        }
        if (column === this.sortedColumn) {
          this.order = this.order === "asc" ? "desc" : "asc";
        } else {
          this.sortedColumn = column;
          this.order = "asc";
        }
        this.fetchData();
      },
      editTextChanged(item, id, value) { },
      editTextInput(item, id, value) {
        this.$emit("inputChanged", item, id, value);
      },
      rowClicked(item, index, event) {
        this.$emit("rowClicked", item, index, event);
      },
      checkboxChanged() {
        var checkedData = [];
        this.tableData.forEach(data => {
          if (data.hasOwnProperty("selected") && data['selected'] == true) {
            checkedData.push(data);
          }
        });
        this.$emit("checkboxChanged",checkedData);
      }
    },
    filters: {
      columnHead(value) {
        return value.split("_").join(" ").toUpperCase();
      },
    },
    name: "DataTable",
  };
  </script>
  
  <style scoped></style>