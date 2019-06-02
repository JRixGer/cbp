<template>
    <div class="modal draw-pad-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">SIGN HERE</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click='processClose'><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="body-wrapper">
                        <div>
                        <VueSignaturePad
                              width="450px"
                              height="500px"
                              ref="signaturePad"
                            />
                         </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="col-md-12">
                        <div style="width:auto; float:right">  
                            <div>
                              <!-- <button class="btn btn-outline btn-secondary" @click="showWithResize">Show</button> -->
                              <button class="btn btn-outline btn-info" @click="undo">Undo</button>
                              <button class="btn btn-outline btn-info" @click="save(whichSig)" data-dismiss="modal" >Save</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';

    export default {
        name: 'MySignaturePad',
        data() {
            return {
                isShow: false,
                whichSig: ''
            }
        },
        created() {
            this.$root.$on("draw_pad",(whichSig)=>{
                this.whichSig = whichSig;

                this.$nextTick(function () {
                    this.showWithResize();
                });

            })

        },
        methods: {
            
            showWithResize() {
                this.isShow = true;
                this.$nextTick(function () {
                    this.$refs.signaturePad.resizeCanvas(this.$refs.signaturePad.$refs.signaturePadCanvas);
                });
            },            
            undo() {
                this.$refs.signaturePad.undoSignature();
            },
            save(whichSig) {
                const { isEmpty, data } = this.$refs.signaturePad.saveSignature();
                this.$root.$emit("show_sig",data, whichSig);
                
                this.$refs.signaturePad.clearSignature();

                // console.log(isEmpty);
                // console.log(data);
            },
            processClose() {
                //this.disableButton = false;
            }               
        }
    };
</script>
<style scoped>
.body-wrapper
{
    margin: 2px;
}
.summary-label-overall-total h5 {
    font-style: italic;
}
.summary-info-overall-total {
    border-bottom: 1pt solid black; 
    padding:0px !important;
    font-style: italic;
    color: #ffaa00;
    font-weight: 600;
}
</style>

<style>

.mx-datepicker {
    width: 165px !important;
}

.mx-input-wrapper {
    position: relative;
    width: 165px  !important;
}


.mx-input {
    font-size: 12px;
}

</style>
