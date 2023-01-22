import React, { useState, useEffect, useCallback } from "react";
import ReactDOM from "react-dom";
import axios from "axios";

const ListProduct = props =>{

    const [data,setData] = useState([]);
    const [meta,setMeta] = useState([]);
    const [more,setMore] = useState('text-truncate');
    const [cPage,setCPage] = useState(1);
    const [flowState,setFlowState] = useState('hidden')


    const getQueryData = (params, page=1, limit=5)=>{
        const title = params.get('title');
        const min = params.get('price_from');
        const max = params.get('price_to');
        const date = params.get('date');

        axios.get(`api/products?page=${page}&limit=${limit}&title=${title}&min=${min}&max=${max}&date=${date}`)
        .then(res=>{
                setData(res.data.data);
            setMeta(res.data.meta);
        
        })
        .catch(err=>{
            console.log(err);
        });
        
    }
    
    const getData = (page=1,limit=5)=>{
        
        axios.get(`api/products?page=${page}&limit=${limit}`)
        .then(res=>{
            setData(res.data.data);
            setMeta(res.data.meta);
            console.log(res.data.meta);
            
        })
        .catch(err=>{
            console.log(err);
        });
    }
    
    
    useEffect(()=>{
        const params = new URLSearchParams(window.location.search)
        if(params.get('title') || params.get('price_from') || params.get('price_to') || params.get('date')){
            getQueryData(params);
        }
        else{
            getData();
        }

    },[])


    return(
        <div className="card">
        <form action="" method="get" className="card-header">
            <div className="form-row justify-content-between">
                <div className="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" className="form-control" />
                </div>
                <div className="col-md-2">
                    <select name="variant" id="" className="form-control">

                    </select>
                </div>
                <div className="col-md-3">
                    <div className="input-group">
                        <div className="input-group-prepend">
                            <span className="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" className="form-control" />
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" className="form-control" />
                    </div>
                </div>
                <div className="col-md-2">
                    <input type="date" name="date" placeholder="Date" className="form-control" />
                </div>
                <div className="col-md-1">
                    <button type="submit" className="btn btn-primary float-right"><i className="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div className="card-body">
            <div className="table-response">
                <table className="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody>
                        {
                            data.map((v,i)=>(   
                                    <tr key={i}>
                                            <td>{v?.id}</td>
                                            <td width="10rem">{v?.title}<br/> Created at : {v?.created_at}</td>
                                            <td width="300rem"><p className={`${more}`} style={{width:"20rem"}}>{v?.description}</p></td>
                                            <td>
                                                <dl className="row mb-0" style={{height: "80px",overflow:flowState}}id="variant">

                                                    {v?.variant_price.map((vp,i)=>(
                                                        <>
                                                        <dt key={i} className="col-sm-3 pb-0">
                                                            {vp?.variant_one?.variant}/ {vp?.variant_two?.variant}/ {vp?.variant_three?.variant}
                                                            {/* SM/ Red/ V-Nick */}
                                                        </dt>
                                                        <dd className="col-sm-9">
                                                            <dl key={i} className="row mb-0">
                                                                <dt className="col-sm-4 pb-0">Price : {vp?.price}  </dt>
                                                                <dd className="col-sm-8 pb-0">InStock : {vp?.stock}  </dd>
                                                            </dl>
                                                        </dd>
                                                        </>

                                                    ))}
                    
                                                </dl>
                                                <button onClick={()=>{flowState=='scroll'?setFlowState('hidden'):setFlowState('scroll')}} className="btn btn-sm btn-link">Show more</button>
                                            </td>
                                            <td>
                                                <div className="btn-group btn-group-sm">
                                                    <a href={`/product/edit/${v?.id}`} className="btn btn-success">Edit</a>
                                                </div>
                                            </td>
                                        </tr>
                    
                                ))

                        }

                    </tbody>

                </table>
            </div>

        </div>

        <div className="card-footer">
            <div className="row justify-content-between">
                <div className="col-md-6">
                    <p>Showing {meta?.offset + 1} to {(Number(meta?.offset) + Number(meta?.limit))} out of {meta?.total}</p>
                </div>
                <div className="col-md-2">

                </div>
            </div>
            <nav aria-label="Page navigation example">
                    <ul className="pagination justify-content-end">
                        <li className="page-item disabled">
                        <a className="page-link" href="#" tabIndex="-1" onClick={()=>{setCPage(cPage-1);getData(cPage-1);}}>Previous</a>
                        </li>
                        <li className="page-item"><a className="page-link" onClick={()=>{getData(1);}}>1</a></li>
                        <li className="page-item"><a className="page-link" onClick={()=>{getData(2);}}>2</a></li>
                        <li className="page-item"><a className="page-link" onClick={()=>{getData(3);}}>3</a></li>
                        <li className="page-item">
                        <a className="page-link" href="#" onClick={()=>{setCPage(cPage+1);getData(cPage+1);}}>Next</a>
                        </li>
                    </ul>
            </nav>
        </div>
    </div>
    )
}

export default ListProduct;

const element = document.getElementById('listProduct');
if(element){
    ReactDOM.render(<ListProduct />,element);
}